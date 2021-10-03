<?php
$bd = new SQLite3('RamTomarDB.sqlite');


$results = $bd->query('SELECT * FROM Edificios');
while ($row = $results->fetchArray()) {
    var_dump($row);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista de Edifícios</title>
</head>
<body>
<div class="container">
    <div class="page-header">
        <h1></h1>
    </div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Edificios</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($results as $row) : ?>
            <tr>

                <td><?php echo $row ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
