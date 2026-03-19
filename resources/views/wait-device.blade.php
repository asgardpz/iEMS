<div style="max-width:560px;margin:64px auto;padding:24px;text-align:center;">
    <h2>裝置登入等待</h2>
    <p>DeviceID: <strong>{{ $device_id }}</strong></p>
    <p>目前狀態：<span id="status">等待中...</span></p>
    <pre id="log" style="text-align:left;background:#f5f5f5;padding:12px;border:1px solid #ccc;max-height:200px;overflow:auto;"></pre>

    <script>
      const deviceId = "{{ $device_id }}";
      const base = "/iEMS/public";
      let retry = 0;
      let expired = false;

      // 設定 30 秒逾時
      setTimeout(() => {
        if (!expired) {
          expired = true;
          const failJson = { result: "FAIL", message: "使用者未登" };
          document.getElementById("status").textContent = "❌ 已逾時，請重新掃描";
          document.getElementById("log").textContent = JSON.stringify(failJson, null, 2);
        }
      }, 30000);

      async function poll() {
        if (expired) return; // 已逾時就停止輪詢

        try {
          const resp = await fetch(`${base}/recive/${encodeURIComponent(deviceId)}`, {cache:"no-store"});
          const data = await resp.json();

          // 顯示 JSON 原始內容
          document.getElementById("log").textContent = JSON.stringify(data, null, 2);

          if (data.result === "OK") {
            expired = true; // 成功就停止逾時判斷
            document.getElementById("status").textContent = `✅ 登入成功：${data.message || "使用者"}`;
            return;
          }
          if (data.result === "FAIL") {
            expired = true;
            document.getElementById("status").textContent = "❌ 已逾時，請重新掃描";
            return;
          }
          retry++;
          document.getElementById("status").textContent = `⏳ 等待中（第 ${retry} 次輪詢）`;
          setTimeout(poll, 2000);
        } catch (e) {
          retry++;
          document.getElementById("status").textContent = `⚠️ 錯誤，重試中（第 ${retry} 次）`;
          setTimeout(poll, 3000);
        }
      }
      poll();
    </script>
</div>
