<section class="hero">
    <div>
        <p><strong>AI-assisted, human-approved</strong></p>
        <h1>Find lost items faster and claim them securely.</h1>
        <p>Report a lost or found item with a photograph and details. FindIt compares image, description, colour, category, location and date, then recommends the five most likely matches.</p>
        <div class="hero-actions">
            <?php if (current_user()): ?>
                <a class="btn btn-light" href="<?= h(url('report_item.php')) ?>">Report an item</a>
                <a class="btn btn-secondary" href="<?= h(url('dashboard.php')) ?>">Open dashboard</a>
            <?php else: ?>
                <a class="btn btn-light" href="<?= h(url('register.php')) ?>">Create account</a>
                <a class="btn btn-secondary" href="<?= h(url('login.php')) ?>">Log in</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="hero-card">
        <h2>How FindIt works</h2>
        <ol>
            <li>Report a lost or found item.</li>
            <li>AI ranks likely matches.</li>
            <li>Review the five recommendations.</li>
            <li>Submit ownership evidence.</li>
            <li>An administrator approves or rejects the claim.</li>
        </ol>
    </div>
</section>
<section class="section grid-3">
    <article class="card"><h2>Detailed reports</h2><p>Capture the photograph, description, colour, category, location and date needed for reliable comparison.</p></article>
    <article class="card"><h2>Advisory AI</h2><p>Matching scores help reduce search effort but never automatically transfer ownership.</p></article>
    <article class="card"><h2>Secure claims</h2><p>Claimants provide evidence and an administrator records the final decision for accountability.</p></article>
</section>
