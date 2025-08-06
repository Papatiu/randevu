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
@endsection

@section('content')
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
@endsection



@section('scripts')
    <script>
        let selectedSportId = null;
        let selectedDate = null;

        function selectSport(sportId, sportName) {
            selectedSportId = sportId;

            document.querySelectorAll('.sport-card').forEach(card => card.classList.remove('selected'));
            document.getElementById('sport-' + sportId).classList.add('selected');

            const reservationContainer = document.getElementById('reservation-container');
            const datesSection = document.getElementById('dates-section');
            const hoursSection = document.getElementById('hours-section');

            reservationContainer.style.display = 'block';
            datesSection.style.display = 'block';
            hoursSection.style.display = 'none'; // Saatleri gizle

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
                        html += `
                        <button onclick="loadHours('${item.date}')" class="btn btn-date ${item.status}">
                            ${formattedDate}
                        </button>
                    `;
                    });
                    document.querySelector('.dates-container').innerHTML = html;
                });
        }

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
                        html = '<p>Bu tarih için uygun saat bulunamadı.</p>';
                    } else {
                        data.forEach(hour => {
                            const isFull = hour.status === 'full';
                            html += `
                            <button onclick="${isFull ? '' : `reserve('${hour.saat}')`}" 
                                class="btn btn-hour ${hour.status}" ${isFull ? 'disabled' : ''}>
                                ${hour.saat}
                            </button>`;
                        });
                    }
                    document.querySelector('.hours-container').innerHTML = html;
                });
        }

        function reserve(time) {
            const sportName = document.getElementById('sport-' + selectedSportId).querySelector('h3').innerText;
            const friendlyDate = new Date(selectedDate).toLocaleDateString('tr-TR', {
                day: 'numeric',
                month: 'long'
            });

            @auth
            // Kullanıcı giriş yapmışsa: Onay sorusu
            Swal.fire({
                title: 'Randevuyu Onayla',
                html: `<b>${sportName}</b> için <br><b>${friendlyDate} - ${time}</b> saatine randevu almak istediğinizden emin misiniz?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Evet, Randevu Al!',
                cancelButtonText: 'İptal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Formu oluştur ve gönder
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('reservation.make') }}';
                    form.innerHTML = `
                    @csrf
                    <input type="hidden" name="sport_id" value="${selectedSportId}">
                    <input type="hidden" name="tarih" value="${selectedDate}">
                    <input type="hidden" name="saat" value="${time}">
                `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        @else
            // Kullanıcı giriş yapmamışsa: Giriş uyarısı
            Swal.fire({
                title: 'Giriş Gerekli',
                text: "Randevu alabilmek için lütfen sisteme giriş yapın veya yeni bir hesap oluşturun.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Giriş Yap',
                cancelButtonText: 'Kayıt Ol',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route('login') }}';
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    window.location.href = '{{ route('register') }}';
                }
            });
        @endauth
        }
    </script>
@endsection
