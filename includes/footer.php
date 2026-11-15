<?php

declare(strict_types=1);


$year = date('Y');
$owner = e(APP_OWNER);

echo <<<HTML
<footer>
    {$year} AthleteLog - Autor: {$owner}
</footer>
</body>
</html>
HTML;