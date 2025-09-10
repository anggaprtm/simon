<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
  <title>404 | Halaman Tidak Ditemukan</title>
  <style>
    body {
      font-family: "Nunito", sans-serif;
      background: #ebedef;
      color: #333;
      text-align: center;
      padding: 50px;
    }
    .cat {
      font-size: 100px;
      transition: transform 0.3s ease, filter 0.3s ease;
    }
    .cat.sad {
      filter: grayscale(80%);
    }
    .cat.happy {
      transform: scale(1.2) rotate(5deg);
      filter: grayscale(0%);
    }
    h1 {
      font-size: 2rem;
      margin-top: 20px;
      color: #1976d2;
    }
    p {
      margin: 15px 0;
    }
    .btn {
      display: inline-block;
      margin-top: 20px;
      padding: 12px 24px;
      background: #1976d2;
      color: white;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      transition: 0.3s;
    }
    .btn:hover {
      background: #0d47a1;
    }
  </style>
</head>
<body>
  <div id="cat" class="cat sad">😿</div>
  <h1>404 - Ruangan Tidak Ditemukan</h1>
  <p>Tom sedih karena halaman yang kamu cari gak ada 🚪</p>
  <a href="{{ url('/') }}" class="btn" 
     onmouseenter="makeHappy()" 
     onmouseleave="makeSad()">🏠 Kembali ke Beranda</a>

  <script>
    const cat = document.getElementById("cat");
    function makeHappy() {
      cat.textContent = "😺";
      cat.classList.remove("sad");
      cat.classList.add("happy");
    }
    function makeSad() {
      cat.textContent = "😿";
      cat.classList.remove("happy");
      cat.classList.add("sad");
    }
  </script>
</body>
</html>
