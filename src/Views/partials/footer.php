</main>
<footer class="footer">
    <p>Shift Scheduler &copy; <?= date('Y') ?> · Secure by design.</p>
</footer>
<script>
    document.querySelectorAll('.nav-card[href]').forEach((link) => {
        if (link.getAttribute('href') === window.location.pathname) {
            link.classList.add('active');
        }
    });
</script>
</body>
</html>
