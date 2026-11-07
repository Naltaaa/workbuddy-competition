# WorkBuddy Auth — MCP Authentication Service

Bagian dari ekosistem **WorkBuddy AI**, bot kebijakan internal PT. Nexaform Indonesia yang berjalan di Discord.

Repositori ini berisi **MCP Authentication Service** berbasis Laravel — komponen yang memverifikasi identitas karyawan sebelum data kebijakan perusahaan dapat diakses.

---

## Gambaran Sistem

User Discord
└─→ @Blue (WorkBuddy AI)
├─→ [1] verify_nip ──→ Laravel MCP Auth Service ──→ MySQL (employees)
│ └─→ JWT (RS256)
└─→ [2] buku_menu / cari_data ──→ n8n Workflow
├─→ Verifikasi JWT
└─→ MySQL (dataset kebijakan)


Alurnya:
1. Karyawan menyebut `@Blue` di Discord beserta NIP dan PIN
2. WorkBuddy memanggil `verify_nip` ke service ini
3. Service memverifikasi kredensial, mengembalikan JWT bertanda tangan RS256
4. WorkBuddy menggunakan JWT tersebut untuk mengakses data kebijakan via n8n
5. Hasil dikirimkan kembali ke karyawan di Discord

---

## Fitur

- **Verifikasi NIP + PIN** dengan bcrypt (12 rounds)
- **JWT RS256** — ditandatangani dengan private key, diverifikasi n8n dengan public key
- **Role-based** — JWT memuat `tier` dan `role` (employee / hr / manager)
- **Keamanan berlapis** — Bridge Key wajib di setiap request, rate limiting, account lockout setelah 5 kegagalan, waktu respons konstan (timing-safe)
- **Audit log** — setiap percobaan login dicatat
- **MCP-ready** — terekspos sebagai MCP Server, langsung dapat dikonsumsi WorkBuddy AI

---

## Tech Stack

| Komponen | Teknologi |
|---|---|
| Framework | Laravel 12 |
| Runtime | PHP 8.3 (Docker) |
| Database | MySQL |
| Auth Token | JWT RS256 (`firebase/php-jwt`) |
| Protokol | MCP (Model Context Protocol) via `laravel-mcp-server` |
| Testing | Pest PHP |

---

## Struktur Penting

app/
├── Http/
│ ├── Controllers/McpVerifyController.php # Endpoint verifikasi
│ └── Middleware/BridgeKeyMiddleware.php # Guard bridge key
├── Services/
│ ├── AuthenticationService.php # Logic verifikasi + lockout
│ ├── TokenService.php # Issue & verify JWT RS256
│ └── AuditService.php # Pencatatan log
└── Mcp/Tools/VerifyNipTool.php # MCP Tool definition


---

## Cara Menjalankan

### Prasyarat
- Docker & Docker Compose

### Setup

```bash
git clone https://github.com/username/workbuddy-competition.git
cd workbuddy-competition

cp .env.example .env
# Isi BRIDGE_KEY, JWT_*, dan DB credentials di .env

docker compose up -d
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
```

### Generate JWT Key Pair

```bash
docker compose exec app php artisan jwt:generate-keys
```

### Jalankan Test

```bash
docker compose exec app php artisan test
```

---

## Konfigurasi mcp.json (WorkBuddy)

```json
{
  "mcpServers": {
    "policy-auth": {
      "url": "http://127.0.0.1:8000/mcp/auth",
      "headers": {
        "X-Bridge-Key": "YOUR_BRIDGE_KEY"
      },
      "timeout": 10
    }
  }
}
```

---

## Testing

13 test case, 26 assertions — semua pass:

PASS Tests\Feature\AuthenticationServiceTest (7 tests)
PASS Tests\Feature\VerifyEndpointTest (6 tests)


Mencakup: login berhasil, PIN salah, NIP tidak ditemukan, akun nonaktif, account lockout, response timing consistency, validasi input, keamanan Bridge Key.

---

## Lisensi

Proyek ini dibuat untuk keperluan kompetisi. © PT. Nexaform Indonesia.