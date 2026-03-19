<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EV CHARGER BACKEND SYSTEM</title>
  <style>
    html, body {
      height: 100%;
      margin: 0;
      background: #000;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Noto Sans TC", Arial;
      color: #111827;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .outer {
      width: 100vw;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .wrap {
      display: flex;
      width: 960px;
      max-width: 100%;
      height: 600px;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 0 32px rgba(0,0,0,0.5);
      background: #fff;
    }
    .left {
      width: 60%;
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 32px;
      background: linear-gradient(135deg, #6fb6ff 0%, #1f4fb8 100%);
    }
    .ev-icon {
      width: 320px;
      height: 160px;
      margin-bottom: 16px;
      object-fit: contain;
    }
    .left-title {
      text-align: center;
      line-height: 1.1;
      margin-top: 8px;
    }
    .left-title .line {
      font-weight: 700;
      font-size: 48px;
      color: #7ef3a0;
      letter-spacing: 0.3px;
    }
    .version {
      position: absolute;
      left: 24px;
      bottom: 16px;
      font-size: 12px;
      color: #ffffff;
    }
    .right {
      width: 40%;
      background: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 32px;
    }
    .panel {
      width: 100%;
      max-width: 360px;
      text-align: center;
    }
    .welcome {
      font-size: 24px;
      font-weight: 600;
      color: #1f2937;
      margin-bottom: 32px;
    }
    .field {
      position: relative;
      margin-bottom: 20px;
      text-align: left;
    }
    .field input {
      width: 80%;
      max-width: 100%;
      border: 1px solid #9ca3af;
      background: #ffffff;
      color: #111827;
      font-size: 16px;
      padding: 14px 40px 14px 12px;
      outline: none;
      border-radius: 6px;
      transition: border-color 120ms ease;
    }
    .field input::placeholder {
      color: #9ca3af;
    }
    .field input:focus {
      border-color: #000000;
    }
    .field label {
      position: absolute;
      left: 10px;
      top: -10px;
      font-size: 14px;
      color: #4b5563;
      background: #ffffff;
      padding: 0 6px;
      line-height: 1;
      pointer-events: none;
    }
    .asterisk {
      color: #ef4444;
    }
    .error {
      font-size: 13px;
      color: #dc2626;
      margin-top: 4px;
      text-align: left;
    }
    .eye {
      position: absolute;
      right: 20px;
      top: 50%;
      transform: translateY(-50%);
      width: 22px;
      height: 22px;
      cursor: pointer;
      opacity: 0.75;
    }
    .eye:hover {
      opacity: 1;
    }
    .row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 4px;
      font-size: 14px;
      color: #4b5563;
    }
    .checkbox {
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    .row a {
      color: #4b5563;
      text-decoration: none;
    }
    .row a:hover {
      text-decoration: underline;
    }
    .btn {
      width: 100%;
      margin-top: 12px;
      background: #2563eb;
      color: #ffffff;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      font-weight: 600;
      padding: 10px 16px;
      cursor: pointer;
      transition: background 120ms ease;
    }
    .btn:hover {
      background: #1e55c9;
    }
    .register {
      margin-top: 24px;
      font-size: 14px;
      color: #4b5563;
    }
    .register a {
      color: #2563eb;
      text-decoration: none;
      font-weight: 500;
    }
    .register a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="outer">
    <div class="wrap">
      <section class="left">
        <img src="{{ asset('assets/images/ev-icon.png') }}" alt="EV Icon" class="ev-icon" />
        <div class="left-title">
          <div class="line">EV CHARGER</div>
          <div class="line">BACKEND SYSTEM</div>
        </div>
        <div class="version">v1.0</div>
      </section>

      <section class="right">
        <div class="panel">
          <div class="welcome">Hello, welcome back!</div>

          @if (session('status'))
            <div class="mb-4 text-sm text-green-600">
              {{ session('status') }}
            </div>
          @endif

          <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <!-- Email -->
            <div class="field">
              <label for="email">Email address<span class="asterisk">*</span></label>
              <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="email@example.com" required autofocus autocomplete="email" />
              @error('email')
                <div class="error">{{ $message }}</div>
              @enderror
            </div>

            <!-- Password -->
            <div class="field">
              <label for="password">Password<span class="asterisk">*</span></label>
              <input id="password" name="password" type="password" placeholder="Please input your password" required autocomplete="current-password" />
              <svg class="eye" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                   onclick="togglePassword()">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
              @error('password')
                <div class="error">{{ $message }}</div>
              @enderror
            </div>

            <!-- Remember me + Forgot Password -->
            <div class="row">
              <label class="checkbox">
                <input type="checkbox" name="remember" />
                <span>Remember me</span>
              </label>
              @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Forgot Password?</a>
              @endif
            </div>

            <button type="submit" class="btn">Log In</button>
          </form>

          @if (Route::has('register'))
            <div class="register">
              Don't have an account?
              <a href="{{ route('register') }}">Sign up</a>
            </div>
          @endif
        </div>
      </section>
    </div>
  </div>

  <script>
    function togglePassword() {
      const input = document.getElementById('password');
      input.type = (input.type === 'password') ? 'text' : 'password';
    }
  </script>
</body>
</html>
