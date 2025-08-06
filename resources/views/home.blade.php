<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Eyyübiye Belediyesi Spor Etkinlikleri</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: sans-serif;
            background: #f3f8fc;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
        }

        html,
        body {
            height: 100%;
        }

        .header {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .header img {
            height: 60px;
        }

        .header h1 {
            margin-top: 10px;
            font-size: 24px;
        }

        .sports {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            padding: 30px;
        }

        .card {
            width: 220px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background: #fff;
            cursor: pointer;
            text-align: center;
            transition: 0.3s;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .card h3 {
            margin: 10px 0;
        }

        footer {
            color: white;
            text-align: center;
            padding: 10px 0;
            text-align: center;
        }

        .content {
            flex: 1;
        }
    </style>
</head>

<body>

    <div class="header">
        <img src="https://www.eyyubiye.bel.tr/images/logo.png" alt="logo">
        <h1>Eyyübiye Belediyesi Spor Etkinlikleri Randevu Sistemi</h1>
    </div>

    <div class="sports">
        @foreach ($sports as $sport)
            <div class="card" onclick="selectSport({{ $sport->id }})">
                <img src="{{ asset('storage/sports/' . $sport->resim) }}" alt="{{ $sport->ad }}">
                <h3>{{ $sport->ad }}</h3>
            </div>
        @endforeach
    </div>

    <footer>
        © {{ date('Y') }} Eyyübiye Belediyesi - Tüm Hakları Saklıdır
    </footer>

    <script>
        function selectSport(id) {
            alert("Seçilen etkinlik ID: " + id);
            // Devamında AJAX ile tarih ve saat getirilebilir
        }
    </script>
    <script>
        function selectSport(id) {
            fetch(`/tarihler/${id}`)
                .then(res => res.json())
                .then(data => {
                    let html = '';
                    data.forEach(item => {
                        let color;
                        if (item.status === 'red') color = 'red';
                        else if (item.status === 'yellow') color = 'orange';
                        else color = 'green';

                        html += `
                    <button onclick="loadHours('${item.date}', ${id})"
                        style="margin:5px; padding:10px; background:${color}; color:white; border:none; border-radius:5px;">
                        ${item.date}
                    </button>
                `;
                    });

                    document.querySelector('.dates').innerHTML = html;
                });
        }

        function loadHours(tarih, sport_id) {
            fetch(`/saatler/${sport_id}/${tarih}`)
                .then(res => res.json())
                .then(data => {
                    let html = `<h3>${tarih}</h3><div style="display:flex; flex-wrap:wrap; gap:10px;">`;
                    data.forEach(hour => {
                        let color = hour.status === 'full' ? 'red' : (hour.status === 'partial' ? 'orange' :
                            'green');
                        html += `<button onclick="reserve('${tarih}', '${hour.saat}', ${sport_id})" 
                        style="padding:5px 10px; background:${color}; color:white; border:none;">
                        ${hour.saat}</button>`;
                    });
                    html += '</div>';
                    document.querySelector('.hours').innerHTML = html;
                });
        }

        function reserve(tarih, saat, sport_id) {
            @auth
            if (confirm(`${tarih} ${saat} için randevu al?`)) {
                fetch('/randevu-al', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            tarih,
                            saat,
                            sport_id
                        })
                    })
                    .then(res => res.json())
                    .then(data => alert(data.message));
            }
        @else
            alert("Lütfen giriş yapın veya kayıt olun.");
            window.location.href = '{{ route('login') }}';
        @endauth
        }
    </script>

</body>

</html>
