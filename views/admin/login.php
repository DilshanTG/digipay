<?php Flight::render('layout_header', ['title' => 'Admin Login']); ?>

<div class="login-container">
    <div class="login-card">
        <div class="login-brand">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <h1>DigiPay Admin</h1>
            <p>Secure Enterprise Gateway</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="login-alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= url('admin/login') ?>" method="POST" class="login-form">
            <div class="form-group">
                <label for="password">System Password</label>
                <div class="input-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="field-icon">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <input type="password" id="password" name="password" placeholder="••••••••" required autofocus>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <span>Access Dashboard</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                </svg>
            </button>
        </form>

        <div class="login-footer">
            <p>&copy; <?= date('Y') ?> DigiMart Solutions. All rights reserved.</p>
        </div>
    </div>
</div>

<style>
    :root {
        --primary: #6366f1;
        --secondary: #4f46e5;
        --bg: #0f172a;
        --card-bg: #1e293b;
        --text: #f8fafc;
        --text-muted: #94a3b8;
    }

    body {
        margin: 0;
        padding: 0;
        background-color: var(--bg);
        color: var(--text);
        font-family: 'Inter', -apple-system, system-ui, sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
    }

    .login-container {
        width: 100%;
        max-width: 440px;
        padding: 20px;
        animation: fadeIn 0.6s ease-out;
    }

    .login-card {
        background: var(--card-bg);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px;
        padding: 48px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    .login-brand {
        text-align: center;
        margin-bottom: 40px;
    }

    .logo-icon {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 16px;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
    }

    .logo-icon svg {
        width: 32px;
        height: 32px;
    }

    .login-brand h1 {
        font-size: 28px;
        font-weight: 800;
        margin: 0;
        letter-spacing: -0.025em;
    }

    .login-brand p {
        color: var(--text-muted);
        margin-top: 8px;
    }

    .login-alert {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        color: #f87171;
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
    }

    .login-alert svg { width: 20px; height: 20px; flex-shrink: 0; }

    .form-group { margin-bottom: 24px; }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 8px;
        color: var(--text-muted);
    }

    .input-wrapper { position: relative; }

    .field-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        color: var(--text-muted);
    }

    .form-group input {
        width: 100%;
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 12px 16px 12px 44px;
        color: white;
        font-size: 16px;
        transition: all 0.2s;
        box-sizing: border-box;
    }

    .form-group input:focus {
        outline: none;
        border-color: var(--primary);
        background: rgba(0, 0, 0, 0.3);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .btn-login {
        width: 100%;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border: none;
        padding: 14px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn-login:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
    }

    .btn-login:active { transform: translateY(0); }

    .btn-login svg { width: 18px; height: 18px; transition: transform 0.2s; }
    .btn-login:hover svg { transform: translateX(4px); }

    .login-footer {
        margin-top: 32px;
        text-align: center;
        font-size: 12px;
        color: var(--text-muted);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<?php Flight::render('layout_footer'); ?>
