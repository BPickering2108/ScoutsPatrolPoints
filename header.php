<header>
    <nav>
        <ul>
            <li class="dropdown">
                <a href="index" class="dropbtn">Home</a>
                <div class="dropdown-content">
                    <a href="monScouts">Monday Scouts</a>
                    <a href="tueExplorers">Tuesday Explorers</a>
                    <a href="wedScouts">Wednesday Scouts</a>
                    <a href="thurExplorers">Thursday Explorers</a>
                </div>
            </li>
            <li><a href="account">Account</a></li>
            <li><a href="settings">Settings</a></li>

            <?php
            if (isset($_SESSION['user_id'])) {
                // If user is logged in, show logout link
                echo '<li class="logout"><a href="backend/logout.php">Logout</a></li>';
            } else {
                // If no session exists, show login link
                echo '<li class="login"><a href="login.php">Login</a></li>';
            }
            ?>
        </ul>
    </nav>
</header>