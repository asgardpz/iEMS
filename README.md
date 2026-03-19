# EV Charger Backend System

這是一個電動車充電樁的後台管理系統，提供完整的帳號、設備、交易與維護管理功能，並支援角色與權限控管。

---

## 系統功能與介面截圖

### Home
系統首頁，進入後台入口  
![Home](https://github.com/asgardpz/iEMS/blob/main/public/home.jpg)

### Login
使用者登入介面，支援帳號密碼驗證  
![Login](https://github.com/asgardpz/iEMS/blob/main/public/LogIn.jpg)

### SignUp
新使用者註冊功能，建立帳號並分配角色  
![SignUp](https://github.com/asgardpz/iEMS/blob/main/public/SignUp.jpg)

### Real-Time Monitoring
即時監控充電樁狀態與使用情況  
![RealTime](https://github.com/asgardpz/iEMS/blob/main/public/Real-Time.jpg)

### Dashboard
系統總覽，顯示主要統計數據與指標  
![Dashboard](https://github.com/asgardpz/iEMS/blob/main/public/Dashboard.jpg)

### Transactions & Billing
交易紀錄查詢、帳單管理與費率設定  
![Transactions](https://github.com/asgardpz/iEMS/blob/main/public/Transactions.jpg)

### Maintenance Management
維護工單建立與追蹤，設備維修紀錄管理  
![Maintenance](https://github.com/asgardpz/iEMS/blob/main/public/Maintenance.jpg)

### Reports & Analytics
報表產生 (PDF/CSV 匯出)，使用數據分析  
![Reports](https://github.com/asgardpz/iEMS/blob/main/public/Reports.jpg)

### Device Management
充電樁設備清單，設備狀態與設定管理  
![Device](https://github.com/asgardpz/iEMS/blob/main/public/Device.jpg)

### Work Order Management
工單建立、分派與進度追蹤  
![WorkOrder](https://github.com/asgardpz/iEMS/blob/main/public/WorkOrder.jpg)

### Member Management
使用者帳號管理，員工/客戶資訊維護  
![Member](https://github.com/asgardpz/iEMS/blob/main/public/Member.jpg)

### Permission Settings
角色與權限控管，功能存取限制設定  
![Permission](https://github.com/asgardpz/iEMS/blob/main/public/Permission.jpg)

### Account Management
帳號建立、編輯、刪除，密碼修改與狀態管理，搜尋與篩選帳號，登入紀錄查詢  
![Account](https://github.com/asgardpz/iEMS/blob/main/public/Account.jpg)

---

## API 說明

本系統除了後台管理介面外，還提供 **前台 Client API** 與 **後台 Server API**，讓充電樁設備能以 JSON 格式與系統資料庫互動。

### Client API
前台模擬介面，提供設備端呼叫 API 的方式，支援：
- 裝置狀態上報（電流、電壓、溫度、功率）
- 裝置動作（如韌體更新）
- 韌體目錄查詢
- 充電 Session 建立與查詢
- 支付紀錄
- 站點資訊

![Client API](https://github.com/asgardpz/iEMS/blob/main/public/ClientAPI.jpg)

### Server API
後台 API UI，提供管理者檢視與操作：
- **Device Status History**：紀錄設備狀態（online, inuse, offline, maintenance）與電流/電壓/溫度/功率等參數
- **Device Actions**：設備動作紀錄（如韌體更新），包含版本、來源 URL、checksum、排程等資訊

![Server API](https://github.com/asgardpz/iEMS/blob/main/public/ServerAPI.jpg)

---

## 系統架構圖 (文字模式)
![Flow](https://github.com/asgardpz/iEMS/blob/main/public/Flow.jpg)
---

## 技術架構

- **後端框架**：Laravel (PHP)
- **前端**：Blade + JavaScript + CSS
- **資料庫**：MySQL
- **版本控制**：GitHub

---

## 安裝與使用

1. Clone 專案：
   ```bash
   git clone https://github.com/asgardpz/iEMS.git

