<footer>
    <p>&copy; <span id="currentYear"></span> Bradley Pickering, Dorset Scouts</p>
    <p>Last Updated: <span id="lastUpdated"></span></p>
</footer>

<script>
    // Auto-update the year
    document.getElementById('currentYear').textContent = new Date().getFullYear();

    // Display the last updated date
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('lastUpdated').textContent = new Date(document.lastModified).toLocaleDateString('en-GB', options);
</script>