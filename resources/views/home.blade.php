@extends('layouts.app')

@section('styles')
    <style>
        .header-title {
            text-align: center;
            margin-bottom: 2rem;
            color: #333;
            font-weight: 600;
        }

        .sports-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
        }

        .sport-card {
            width: 250px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            background: #fff;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
            overflow: hidden;
            border: 3px solid transparent;
        }

        .sport-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .sport-card.selected {
            border-color: #0d6efd;
            transform: scale(1.05);
        }

        .sport-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }

        .sport-card h3 {
            margin: 15px 0;
            font-size: 20px;
            color: #333;
        }

        .reservation-section {
            display: none;
            margin-top: 40px;
            padding: 30px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            text-align: center;
            color: #555;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .dates-container,
        .hours-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .btn-date {
            padding: 10px 15px;
            font-size: 0.9rem;
            border-radius: 8px;
        }

        .btn-date.green {
            background-color: #198754;
            color: white;
        }

        .btn-date.yellow {
            background-color: #ffc107;
            color: black;
        }

        .btn-date.red {
            background-color: #dc3545;
            color: white;
        }

        .btn-hour {
            padding: 8px 20px;
            font-size: 1rem;
        }

        .btn-hour.empty {
            background-color: #198754;
            color: white;
        }

        .btn-hour.full {
            background-color: #dc3545;
            color: white;
            cursor: not-allowed;
        }
    </style>
    <style>
        /* ... (diğer stillerin burada, aynı kalıyor) ... */

        /* YENİ NOTLAR BÖLÜMÜ STİLLERİ */
        .rules-container {
            background-color: #fff9f9;
            /* Hafif kırmızımsı arka plan */
            border: 2px solid #dc3545;
            /* Kırmızı çerçeve */
            border-left: 8px solid #dc3545;
            /* Sol kenarı daha kalın yaparak vurgu katıyoruz */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .rules-title {
            color: #b02a37;
            /* Başlık rengi, kırmızıya yakın */
            font-weight: 600;
        }

        #notes-section .list-group-item {
            font-size: 1.05rem;
            /* Yazıyı biraz büyütelim */
            line-height: 1.6;
            color: #495057;
            /* Yazı rengi */
        }

        #notes-section .list-group-item:not(:last-child) {
            border-bottom: 1px solid #fde2e4 !important;
            /* Maddeler arasına ince kırmızımsı çizgi */
        }

        #notes-section .list-group-item strong {
            color: #343a40;
            /* Kalın yazıları biraz daha koyu yapalım */
        }

        .announcement-entry .announcement-title {
            color: #0d6efd;
            margin-bottom: 0.5rem;
        }

        .announcement-entry .announcement-content {
            color: #495057;
            line-height: 1.6;
        }
    </style>
@endsection

@section('content')
    @if (isset($announcements) && $announcements->count() > 0)
        <div class="container mb-4">
            <div class="alert alert-info p-2" role="alert">
                <marquee behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();"
                    style="cursor: pointer; align-items: center; display: flex;">
                    @foreach ($announcements as $announcement)
                        <span class="mx-4">
                            <i class="fas fa-bullhorn me-2"></i>
                            <!-- FontAwesome ikonu (layouts/app.blade.php'de linki olmalı) -->
                            <strong>{{ $announcement->title }}:</strong> {{ strip_tags($announcement->content) }}
                        </span>
                    @endforeach
                </marquee>
            </div>
        </div>
    @endif

  


    <div class="container">
        {{-- BURAYI EKLE! --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        {{-- EKLENECEK KISIM BİTTİ --}}
        <h1 class="header-title">Spor Etkinlikleri Randevu Sistemi</h1>

        <div class="sports-container">
            @foreach ($sports as $sport)
                <div class="sport-card" id="sport-{{ $sport->id }}"
                    onclick="selectSport({{ $sport->id }}, '{{ $sport->ad }}')">
                    <img src="{{ asset('storage/sports/' . $sport->resim) }}" alt="{{ $sport->ad }}">
                    <h3>{{ $sport->ad }}</h3>
                </div>
            @endforeach

        </div>

        @if (isset($notes) && $notes->count() > 0)
            <div id="notes-section" class="container mt-5 mb-5">
                <div class="rules-container p-4 rounded">
                    <h3 class="text-center mb-4 rules-title">
                        <i class="fas fa-scroll me-2"></i> <!-- Başlık İkonu -->
                        <p style="font-weight: bold; border-bottom: 2px solid red; padding-bottom: 3px;">
                            Önemli Notlar ve Kurallar
                        </p>
                    </h3>
                    <div class="list-group list-group-flush">
                        @foreach ($notes as $note)
                            <div class="list-group-item d-flex align-items-start bg-transparent border-0 px-0">
                                <i class="fas fa-check-circle text-success me-3 mt-1"></i> <!-- Madde Başı İkonu -->
                                <div class="flex-grow-1">
                                    {!! $note->content !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div id="reservation-container" class="reservation-section">
            <div id="dates-section" style="display: none;">
                <h4 class="section-title" id="date-title"></h4>
                <div class="dates-container"></div>
            </div>
            <div id="hours-section" class="mt-4" style="display: none;">
                <h4 class="section-title" id="hour-title"></h4>
                <div class="hours-container"></div>
            </div>
        </div>
    </div>
    <!-- ================================================================= -->
    <!--         YENİ VE DÜZENLENMİŞ BİLGİ GİRİŞ MODALI                    -->
    <!-- ================================================================= -->
    <div class="modal fade" id="guestInfoModal" tabindex="-1" aria-labelledby="guestInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg"> {{-- Daha geniş bir modal için modal-lg eklendi --}}
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="guestInfoModalLabel">Randevu için Bilgilerinizi Girin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="guest-info-form">
                        {{-- Bilgi Giriş Alanları --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tc_kimlik" class="form-label">TC Kimlik Numarası</label>
                                <input type="text" class="form-control" id="tc_kimlik" maxlength="11"
                                    placeholder="TC Kimlik Numaranız">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="dogum_yili" class="form-label">Doğum Yılı</label>
                                <input type="number" class="form-control" id="dogum_yili" placeholder="Örn: 1990"
                                    min="1920" max="{{ date('Y') - 18 }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ad" class="form-label">Ad</label>
                                <input type="text" class="form-control" id="ad" placeholder="Adınız">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="soyad" class="form-label">Soyad</label>
                                <input type="text" class="form-control" id="soyad" placeholder="Soyadınız">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="telefon" class="form-label">Telefon Numarası</label>
                            <input type="text" class="form-control" id="telefon" placeholder="5XXXXXXXXX">
                        </div>

                        {{-- KVKK Onay Kutuları --}}
                        <hr>
                        <div class="kvkk-section mt-3" style="font-size: 0.9rem;">
                            <div class="form-check mb-3">
                                <input class="form-check-input kvkk-check" type="checkbox" value=""
                                    id="kvkk1">
                                <label class="form-check-label" for="kvkk1">
                                    Eyyübiye Belediyesi Tesislerinde spor yapmama engel herhangi bir sağlık problemim
                                    olmadığını beyan ederim.
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input kvkk-check" type="checkbox" value=""
                                    id="kvkk2">
                                <label class="form-check-label" for="kvkk2">
                                    Tesis kullanım kurallarını kabul ediyorum. <a href="#" target="_blank">Kullanım
                                        kuralları için tıklayınız.</a>
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input kvkk-check" type="checkbox" value=""
                                    id="kvkk3">
                                <label class="form-check-label" for="kvkk3">
                                    EYYÜBİYE BELEDİYESİ tarafından bu başvuru formunda yer alan kişisel verilerimin
                                    işlenmesi ve korunması ile ilgili <a href="#" target="_blank">aydınlatma
                                        metnini</a> okudum ve anladım.
                                </label>
                            </div>
                        </div>

                        {{-- Opsiyonel İletişim İzni Kutusu --}}
                        <div class="kvkk-section mt-3 p-3 bg-light rounded border">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="kvkk-sms">
                                <label class="form-check-label fw-bold" for="kvkk-sms">
                                    EYYÜBİYE BELEDİYESİ tarafından iletişim (SMS, E-POSTA vb.) kanallarıyla belediyenin
                                    duyurularının iletilmesi amacıyla iletişim verilerimin işlenmesine rıza veriyorum.
                                </label>
                            </div>
                        </div>

                        {{-- Hata Mesajı Alanı --}}
                        <div id="info-error" class="text-danger mt-3 fw-bold text-center"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" onclick="checkKvkkAndVerify()">Doğrula ve Devam
                        Et</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. SMS Onay Modalı -->
    <div class="modal fade" id="smsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">SMS Doğrulama</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Telefonunuza gönderilen 6 haneli doğrulama kodunu giriniz.</p>
                    <div id="sms-form">
                        <div class="mb-3">
                            <label for="sms_code" class="form-label">Doğrulama Kodu</label>
                            <input type="text" class="form-control" id="sms_code" maxlength="6">
                        </div>
                        <div id="sms-error" class="text-danger mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" onclick="confirmAppointment()">Randevuyu
                        Onayla</button>
                </div>
            </div>
        </div>
    </div>
    @if (isset($announcements) && $announcements->count() > 0)
        <div class="modal fade" id="announcementPopup" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-bullhorn me-2"></i> Güncel Duyurular</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        @foreach ($announcements as $index => $announcement)
                            <div class="announcement-entry">
                                <h4 class="announcement-title">{{ $announcement->title }}</h4>
                                <div class="announcement-content">
                                    {!! $announcement->content !!}
                                </div>
                            </div>
                            @if (!$loop->last)
                                <hr class="my-4">
                            @endif
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sadece duyuru varsa bu bloğu çalıştır
            @if (isset($announcements) && $announcements->count() > 0)
                // Kullanıcının bu oturumunda duyuruyu daha önce görüp görmediğini kontrol et
                if (!sessionStorage.getItem('announcementPopupShown')) {
                    // Eğer görmediyse, modalı göster
                    const announcementModal = new bootstrap.Modal(document.getElementById('announcementPopup'));
                    announcementModal.show();
                    // Gördü olarak işaretle ki bir daha açılmasın
                    sessionStorage.setItem('announcementPopupShown', 'true');
                }
            @endif
        });

        // --- GLOBAL DEĞİŞKENLER ---
        let selectedSportId = null;
        let selectedDate = null;
        let selectedTime = null;
        let selectedSlotId = null; // Randevu alınacak slot'un ID'si

        // Bootstrap Modal nesnelerini JavaScript'te tanımla
        const guestInfoModal = new bootstrap.Modal(document.getElementById('guestInfoModal'));
        const smsModal = new bootstrap.Modal(document.getElementById('smsModal'));

        // --- SENİN ÇALIŞAN KODUN (DOKUNULMADI) ---
        function selectSport(sportId, sportName) {
            selectedSportId = sportId;
            document.querySelectorAll('.sport-card').forEach(card => card.classList.remove('selected'));
            document.getElementById('sport-' + sportId).classList.add('selected');

            const reservationContainer = document.getElementById('reservation-container');
            const notesSection = document.getElementById('notes-section'); // Notlar bölümünü bul
            const datesSection = document.getElementById('dates-section');
            const hoursSection = document.getElementById('hours-section');

            // ---- İSTEK 3: Notları gizle ----
            if (notesSection) {
                notesSection.style.display = 'none'; // Notlar bölümünü gizle
            }
            // -----------------------------

            reservationContainer.style.display = 'block';
            datesSection.style.display = 'block';
            hoursSection.style.display = 'none';

            document.getElementById('date-title').innerText = `'${sportName}' için uygun bir tarih seçin:`;
            document.querySelector('.dates-container').innerHTML =
                '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Yükleniyor...</span></div>';

            fetch(`/tarihler/${sportId}`)
                .then(res => res.json())
                .then(data => {
                    let html = '';
                    data.forEach(item => {
                        const date = new Date(item.date);
                        const formattedDate = date.toLocaleDateString('tr-TR', {
                            day: 'numeric',
                            month: 'long',
                            weekday: 'long'
                        });
                        html +=
                            `<button onclick="loadHours('${item.date}')" class="btn btn-date ${item.status}">${formattedDate}</button>`;
                    });
                    document.querySelector('.dates-container').innerHTML = html;
                    reservationContainer.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
        }

        // --- SENİN ÇALIŞAN KODUN (KÜÇÜK BİR GÜNCELLEME İLE) ---
        function loadHours(date) {
            selectedDate = date;
            const hoursSection = document.getElementById('hours-section');
            hoursSection.style.display = 'block';
            const formattedDate = new Date(date).toLocaleDateString('tr-TR', {
                day: 'numeric',
                month: 'long'
            });
            document.getElementById('hour-title').innerText = `${formattedDate} için uygun bir saat seçin:`;
            document.querySelector('.hours-container').innerHTML =
                '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Yükleniyor...</span></div>';

            fetch(`/saatler/${selectedSportId}/${date}`)
                .then(res => res.json())
                .then(data => {
                    let html = '';
                    if (data.length === 0) {
                        html = '<p class="text-muted">Bu tarih için uygun saat bulunamadı.</p>';
                    } else {
                        data.forEach(hour => {
                            const isFull = hour.status === 'full';
                            // GÜNCELLEME: onclick fonksiyonuna artık slot_id'yi de yolluyoruz.
                            html +=
                                `<button onclick="${isFull ? '' : `reserve('${hour.saat}', ${hour.slot_id})`}" class="btn btn-hour ${hour.status}" ${isFull ? 'disabled' : ''}>${hour.saat}</button>`;
                        });
                    }
                    document.querySelector('.hours-container').innerHTML = html;
                    hoursSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                });
        }

        // --- YENİ MİSAFİR RANDEVU SİSTEMİ İÇİN `reserve()` VE YENİ FONKSİYONLAR ---
        function reserve(time, slotId) {
            // Seçilen saat ve slot bilgilerini global değişkenlere ata
            selectedTime = time;
            selectedSlotId = slotId;

            // Misafir kullanıcı olduğu için doğrudan bilgi giriş modalını aç
            guestInfoModal.show();
        }

        function checkKvkkAndVerify() {
            const infoError = document.getElementById('info-error');
            const requiredCheckboxes = document.querySelectorAll('.kvkk-check');
            let allChecked = true;

            // Gerekli tüm checkbox'lar işaretli mi diye kontrol et
            requiredCheckboxes.forEach(checkbox => {
                if (!checkbox.checked) {
                    allChecked = false;
                }
            });

            if (allChecked) {
                infoError.textContent = ''; // Hata mesajını temizle
                verifyIdentity(); // Eğer hepsi işaretliyse, NVI doğrulama fonksiyonunu çalıştır
            } else {
                // Eğer en az biri işaretli değilse hata göster
                infoError.textContent = 'Lütfen devam etmeden önce gerekli tüm onayları işaretleyiniz.';
            }
        }


        function verifyIdentity() {
            const infoError = document.getElementById('info-error');
            infoError.textContent = '';

            // YENİ: Butonu geçici olarak devre dışı bırakıp bekleme efekti ekleyelim
            const verifyButton = document.querySelector('#guestInfoModal .btn-primary');
            verifyButton.disabled = true;
            verifyButton.innerHTML = `
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        Doğrulanıyor...
    `;

            const payload = {
                _token: '{{ csrf_token() }}',
                slot_id: selectedSlotId,
                tc_kimlik: document.getElementById('tc_kimlik').value,
                ad: document.getElementById('ad').value,
                soyad: document.getElementById('soyad').value,
                dogum_yili: document.getElementById('dogum_yili').value,
                telefon: document.getElementById('telefon').value,
            };

            fetch('/randevu/kimlik-dogrula', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Bilgiler doğru, bilgi modalını kapat
                        guestInfoModal.hide();

                        // =================================================================
                        // ===      CHROME ALERT YERİNE ŞIK SWEETALERT BİLDİRİMİ       ===
                        // =================================================================
                        Swal.fire({
                            icon: 'success',
                            title: 'Doğrulama Kodu Gönderildi!',
                            text: data
                                .message, // Backend'den gelen "Doğrulama kodu telefonunuza SMS olarak gönderildi." mesajı
                            timer: 3000, // 3 saniye sonra otomatik kapanır
                            timerProgressBar: true,
                            showConfirmButton: false, // OK butonu olmasın
                        }).then(() => {
                            // Bildirim kapandıktan sonra SMS modalını aç
                            smsModal.show();
                        });

                    } else {
                        // Bilgiler yanlış, hata mesajını göster
                        infoError.textContent = data.message || 'Bir hata oluştu.';
                    }
                }).catch(() => {
                    infoError.textContent = 'Sunucuyla bağlantı kurulamadı.';
                }).finally(() => {
                    // YENİ: İşlem bitince (başarılı veya başarısız) butonu eski haline getir
                    verifyButton.disabled = false;
                    verifyButton.innerHTML = 'Doğrula ve Devam Et';
                });
        }

        function confirmAppointment() {
            const smsError = document.getElementById('sms-error');
            smsError.textContent = '';

            const payload = {
                _token: '{{ csrf_token() }}',
                sms_code: document.getElementById('sms_code').value
            };

            fetch('/randevu/onayla', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        smsModal.hide();
                        const details = data.appointment_details;
                        Swal.fire({
                            icon: 'success',
                            title: 'Randevunuz Başarıyla Oluşturuldu!',
                            html: `
                        <b>Spor Dalı:</b> ${details.sport_name}<br>
                        <b>Tarih:</b> ${new Date(details.date).toLocaleDateString('tr-TR', {day: 'numeric', month: 'long'})}<br>
                        <b>Saat:</b> ${details.time}<br>
                        <hr>
                        <p class="mb-1">Lütfen randevu takip ve iptal  işlemleri için aşağıdaki kodu saklayınız.</p>
                        <h3>Randevu Kodunuz: <b>${details.cancel_code}</b></h3>
                    `,
                            confirmButtonText: 'Tamamdır!'
                        }).then(() => {
                            // Saat listesini yenileyerek dolu saatin kırmızı görünmesini sağla
                            loadHours(selectedDate);
                        });
                    } else {
                        smsError.textContent = data.message || 'Doğrulama sırasında bir hata oluştu.';
                    }
                }).catch(() => smsError.textContent = 'Sunucuya bağlanırken bir hata oluştu.');
        }
    </script>
@endsection
