<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <title>Kami Lagi Maintenance 😺</title>
    <style>
        body {
            font-family: "Nunito", sans-serif;
            background: #ebedef;
            color: #333;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
            padding: 1rem;
        }
        h1 {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }
        .cat {
            width: 200px;
            height: 200px;
            background: url('https://media.giphy.com/media/JIX9t2j0ZTN9S/giphy.gif') no-repeat center;
            background-size: cover;
            border-radius: 50%;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
        }
        .footer {
            margin-top: 2rem;
            font-size: 0.9rem;
            color: #777;
            text-align: center;
        }
        .paw {
            position: absolute;
            font-size: 2rem;
            animation: fadeout 1.5s forwards;
            pointer-events: none;
        }
        @keyframes fadeout {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(2); }
        }
        .meow {
            position: absolute;
            font-size: 1.5rem;
            font-weight: bold;
            color: #ff6699;
            animation: float 2s forwards;
            pointer-events: none;
        }
        @keyframes float {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-50px); }
        }

        /* 📱 Responsiveness */
        @media (max-width: 768px) {
            h1 {
                font-size: 2.2rem;
            }
            p {
                font-size: 1rem;
            }
            .cat {
                width: 150px;
                height: 150px;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.8rem;
            }
            p {
                font-size: 0.9rem;
            }
            .cat {
                width: 120px;
                height: 120px;
            }
            .footer {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <h1>😺 Kami Lagi Maintenance</h1>
    <p>Mimin websitenya lagi tidur siang sebentar...<br>silakan kembali beberapa saat lagi!</p>

    <div class="cat"></div>

    <div class="footer">
        &copy; {{ date('Y') }} Fakultas Teknologi Maju dan Multidisiplin. Semua hak dicakar 🐾
    </div>

    <script>
        // 🐾 Tapak kaki saat klik
        document.addEventListener("click", (e) => {
            const paw = document.createElement("div");
            paw.className = "paw";
            paw.style.left = e.pageX + "px";
            paw.style.top = e.pageY + "px";
            paw.innerHTML = "🐾";
            document.body.appendChild(paw);
            setTimeout(() => paw.remove(), 1500);
        });

        // 🐱 Random teks "Meow~"
        const meows = ["Meow~", "Nyaa~", "Purr...", "Miaw~"];
        function spawnMeow() {
            const meow = document.createElement("div");
            meow.className = "meow";
            meow.innerHTML = meows[Math.floor(Math.random() * meows.length)];
            meow.style.left = Math.random() * window.innerWidth + "px";
            meow.style.top = Math.random() * window.innerHeight + "px";
            document.body.appendChild(meow);
            setTimeout(() => meow.remove(), 2000);
        }
        setInterval(spawnMeow, 3000); // tiap 3 detik muncul
    </script>
</body>
</html>
