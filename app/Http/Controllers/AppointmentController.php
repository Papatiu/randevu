<?php

namespace App\Http\Controllers;

use App\Models\BannedTc;
use App\Models\Setting;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Slot;
use App\Models\Appointment;
use Log;
use SoapClient; // SoapClient'ı kullanabilmek için bunu ekliyoruz.
use Exception; // Hata yakalamak için bunu ekliyoruz.


class AppointmentController extends Controller
{
    /**
     * Adım 1: Kimlik bilgilerini doğrula ve SMS gönder.
     */// app/Http/Controllers/AppointmentController.php

    // app/Http/Controllers/AppointmentController.php

    // Dosyanın en üstüne 'use' ifadelerini eklediğinden emin o
    // 

    public function verifyIdentityAndSendSms(Request $request, SmsService $smsService) // <-- $smsService'i buraya ekledik
    {
        $data = $request->validate([
            'tc_kimlik' => 'required|string|size:11',
            'ad' => 'required|string|max:255',
            'soyad' => 'required|string|max:255',
            'dogum_yili' => 'required|digits:4',
            'telefon' => 'required|string|max:15',
        ]);

        // 1. BAN KONTROLÜ
        $bannedCheck = BannedTc::where('tc_kimlik', $data['tc_kimlik'])
            ->where('ban_bitis_tarihi', '>', Carbon::now())
            ->first();

        if ($bannedCheck) {
            $banBitis = Carbon::parse($bannedCheck->ban_bitis_tarihi)->format('d/m/Y H:i');
            return response()->json([
                'success' => false,
                'message' => "Randevunuza gelmediğiniz için {$banBitis} tarihine kadar yeni randevu alamazsınız."
            ]);
        }

        // 2. NVI DOĞRULAMASI
        $nviDogrulandi = false;
        try {
            $client = new SoapClient("https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx?WSDL");
            $response = $client->TCKimlikNoDogrula([
                'TCKimlikNo' => (int) $data['tc_kimlik'],
                'Ad' => Str::upper($data['ad']),
                'Soyad' => Str::upper($data['soyad']),
                'DogumYili' => (int) $data['dogum_yili']
            ]);
            $nviDogrulandi = $response->TCKimlikNoDogrulaResult;
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Kimlik doğrulama servisinde geçici bir sorun oluştu.']);
        }

        if (!$nviDogrulandi) {
            return response()->json(['success' => false, 'message' => 'Girdiğiniz kimlik bilgileri doğrulanamadı. Bilgilerinizi Kontrol Ediniz.']);
        }

        // 3. YAŞ KONTROLÜ
        $dogumYili = (int) $data['dogum_yili'];
        $mevcutYil = Carbon::now()->year;
        $yas = $mevcutYil - $dogumYili;

        if ($yas < 18) {
            return response()->json([
                'success' => false,
                'message' => 'Randevu alabilmek için 18 yaşından büyük olmanız gerekmektedir.'
            ]);
        }

        // 4. MEVCUT RANDEVU KONTROLÜ
        $slot = Slot::find($request->input('slot_id'));
        $existingAppointment = Appointment::where('tc_kimlik', $data['tc_kimlik'])
            ->whereHas('slot', function ($query) use ($slot) {
                $query->where('tarih', $slot->tarih)->where('saat', $slot->saat);
            })
            ->whereIn('durum', ['onay_bekliyor', 'onaylandi'])
            ->first();

        if ($existingAppointment) {
            return response()->json([
                'success' => false,
                'message' => 'Bu tarih ve saatte zaten başka bir etkinliğe randevunuz bulunmaktadır.'
            ]);
        }

        // 5. TÜM KONTROLLER BAŞARILI, GERÇEK SMS GÖNDER VE VERİYİ SAKLA
        $smsCode = rand(100000, 999999);
        $message = "Eyyubiye Belediyesi Spor Randevu sistemine giris icin dogrulama kodunuz: {$smsCode}";

        // GERÇEK SMS GÖNDERMEK İÇİN YAZDIĞIMIZ SERVİSİ ÇAĞIRIYORUZ
        $smsSent = $smsService->sendSms($data['telefon'], $message);

        if (!$smsSent) {
            // SMS gönderilemezse kullanıcıya hata döndür (IP kısıtlaması, kredi bitmesi vb. nedenlerle)
            return response()->json([
                'success' => false,
                'message' => 'Telefonunuza doğrulama kodu gönderilirken bir sorun oluştu. Lütfen bilgilerinizi kontrol edin veya daha sonra tekrar deneyin.'
            ]);
        }

        // SMS başarılıysa, kodu ve diğer bilgileri session'a kaydet
        $request->session()->put('appointment_data', [
            'slot_id' => $request->input('slot_id'),
            'tc_kimlik' => $data['tc_kimlik'],
            'ad' => $data['ad'],
            'soyad' => $data['soyad'],
            'dogum_yili' => $data['dogum_yili'],
            'telefon' => $data['telefon'],
            'sms_code' => $smsCode,
            'team_name' => $data['team_name'] ?? null,           // YENİ
            'participant_count' => $data['participant_count'] ?? null, // YENİ
        ]);

        // Kullanıcıya artık test kodunu göndermiyoruz, SMS'i kontrol etmesini istiyoruz.
        return response()->json(['success' => true, 'message' => "Doğrulama kodu telefonunuza SMS olarak gönderildi."]);
    }


    /**
     * Adım 2: SMS kodunu doğrula ve randevuyu oluştur.
     */
    public function confirmAppointment(Request $request, SmsService $smsService) // <-- $smsService'i buraya da ekledik
    {
        $smsCode = $request->validate(['sms_code' => 'required|string|size:6'])['sms_code'];

        $appointmentData = $request->session()->get('appointment_data');

        if (!$appointmentData || $appointmentData['sms_code'] != $smsCode) {
            return response()->json(['success' => false, 'message' => 'Geçersiz doğrulama kodu.']);
        }

        $slot = Slot::lockForUpdate()->find($appointmentData['slot_id']);
        if ($slot->rezervasyon_sayisi >= $slot->kapasite) {
            $request->session()->forget('appointment_data'); // Session'ı temizle ki kullanıcı tekrar deneyebilsin
            return response()->json(['success' => false, 'message' => 'Maalesef siz işlemi tamamlarken bu saat doldu.']);
        }

        $iptalKodu = strtoupper(Str::random(8));

        $appointment = Appointment::create([
            'slot_id' => $appointmentData['slot_id'],
            'tc_kimlik' => $appointmentData['tc_kimlik'],
            'ad' => $appointmentData['ad'],
            'soyad' => $appointmentData['soyad'],
            'dogum_yili' => $appointmentData['dogum_yili'],
            'telefon' => $appointmentData['telefon'],
            'iptal_kodu' => $iptalKodu,
            'durum' => 'onaylandi',
            'team_name' => $appointmentData['team_name'] ?? null,           // YENİ
            'participant_count' => $appointmentData['participant_count'] ?? null, // YENİ
        ]);

        $slot->increment('rezervasyon_sayisi');

        // =================================================================
        // ===          YENİ EKLENEN ONAY SMS'İ GÖNDERME KODU            ===
        // =================================================================
        try {
            $tarih = Carbon::parse($slot->tarih)->format('d/m/Y');
            $onayMesaji = "Eyyubiye Belediyesi Spor Tesisleri randevunuz olusturulmustur. Tesis: {$slot->sport->ad}, Tarih: {$tarih}, Saat: {$slot->saat}. Iptal Kodu: {$iptalKodu}";

            $smsService->sendSms($appointmentData['telefon'], $onayMesaji);

            Log::info("Onay SMS'i basariyla gonderildi: " . $appointmentData['telefon']);
        } catch (Exception $e) {
            // SMS gitmezse bile randevu oluşturulduğu için işlemi durdurmuyoruz, sadece logluyoruz.
            Log::error("Onay SMS'i gonderilirken hata olustu: " . $e->getMessage());
        }
        // =================================================================

        $request->session()->forget('appointment_data');

        return response()->json([
            'success' => true,
            'message' => 'Randevunuz başarıyla oluşturuldu!',
            'appointment_details' => [
                'sport_name' => $slot->sport->ad,
                'date' => $slot->tarih,
                'time' => $slot->saat,
                'full_name' => $appointmentData['ad'] . ' ' . $appointmentData['soyad'],
                'cancel_code' => $iptalKodu,
            ]
        ]);
    }
    public function findAppointment(Request $request)
    {
        $code = $request->validate(['cancel_code' => 'required|string'])['cancel_code'];

        $appointment = Appointment::where('iptal_kodu', $code)
            ->where('durum', 'onaylandi')
            ->with('slot.sport')
            ->first();

        if (!$appointment) {
            return response()->json(['success' => false, 'message' => 'Bu koda ait aktif bir randevu bulunamadı.']);
        }

        // =======================================================
        // ===            YENİ İPTAL SÜRESİ KONTROLÜ           ===
        // =======================================================
        $cancelDeadlineHours = Setting::where('key', 'cancel_deadline_hours')->value('value') ?? 24; // Varsayılan 24 saat

        $appointmentDateTime = Carbon::parse($appointment->slot->tarih . ' ' . explode(' - ', $appointment->slot->saat)[0]);
        $now = Carbon::now();

        // Randevuya kalan saat, belirlenen limitten az mı?
        if ($now->diffInHours($appointmentDateTime, false) < $cancelDeadlineHours) {
            return response()->json([
                'success' => false,
                'is_cancellable' => false, // Javascript'e iptal edilemez olduğunu söyle
                'message' => "Randevunuza {$cancelDeadlineHours} saatten az bir süre kaldığı için iptal işlemi yapamazsınız."
            ]);
        }

        return response()->json([
            'success' => true,
            'is_cancellable' => true, // Javascript'e iptal edilebilir olduğunu söyle
            'appointment' => [ /* ... */]
        ]);
    }

    /**
     * ID'si verilen randevuyu iptal eder.
     */
    public function cancelAppointment(Request $request)
    {
        $id = $request->validate(['appointment_id' => 'required|integer'])['appointment_id'];

        $appointment = Appointment::where('id', $id)
            ->where('durum', 'onaylandi')
            ->first();

        if (!$appointment) {
            return response()->json(['success' => false, 'message' => 'İptal edilecek randevu bulunamadı veya zaten iptal edilmiş.']);
        }

        // İlişkili slot'u bul ve rezervasyon sayısını bir azalt
        $slot = Slot::find($appointment->slot_id);
        if ($slot) {
            $slot->decrement('rezervasyon_sayisi');
        }

        // Randevunun durumunu güncelle
        $appointment->durum = 'iptal_edildi';
        $appointment->aciklama = 'Kullanıcı tarafından iptal edildi.';
        $appointment->save();

        return response()->json(['success' => true, 'message' => 'Randevunuz başarıyla iptal edildi.']);
    }

}