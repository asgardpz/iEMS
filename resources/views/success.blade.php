<x-layouts.app :title="__('QRCode 登入成功')">
  <style>
    body {
      background-color: #f0f2f5;
      font-family: "Segoe UI", sans-serif;
    }
    .success-box {
      max-width: 480px;
      margin: 80px auto;
      padding: 32px;
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      text-align: center;
      color: #333; /* 深色字體 */
    }
    .success-box h2 {
      font-size: 26px;
      margin-bottom: 12px;
      color: #28a745; /* 綠色成功字樣 */
    }
    .success-box p {
      font-size: 18px;
      margin-bottom: 8px;
    }
    .countdown {
      font-size: 22px;
      font-weight: bold;
      color: #007bff; /* 藍色倒數字 */
      margin-top: 16px;
    }
    .btn {
      margin-top: 20px;
      padding: 10px 20px;
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .btn:hover {
      background: #0056b3;
    }
  </style>

  <div class="success-box">
    <h2>{{ $result }}</h2>
    <p>歡迎登入，{{ $message }}</p>
    <p>系統將在 <span id="countdown">10</span> 秒後自動導向 Dashboard</p>
    <div class="countdown">請稍候...</div>
    <button class="btn" onclick="window.location.href='{{ route('dashboard') }}'">立即前往 Dashboard</button>
  </div>

  <script>
    let seconds = 10;
    const countdownEl = document.getElementById("countdown");

    const timer = setInterval(() => {
      seconds--;
      countdownEl.textContent = seconds;
      if (seconds <= 0) {
        clearInterval(timer);
        window.location.href = "{{ route('dashboard') }}";
      }
    }, 1000);
  </script>
</x-layouts.app>
