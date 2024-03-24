<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Done</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        h3 {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2>Proof of Work</h2>
    <h3>Work Done by Mantas</h3>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
        <?php
                require 'config.php';
                $totalMantas = 0;
                $sql = "SELECT * FROM work_done WHERE name = 'Mantas'";
                if ($result = $conn->query($sql)) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['description'] . "</td>";
                        echo "<td>" . $row['amount'] . "</td>";
                        echo "</tr>";
                        $totalMantas += $row['amount'];
                    }
                    echo "<tr>";
                    echo "<td colspan='2'><strong>Total</strong></td>";
                    echo "<td><strong>$totalMantas</strong></td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>

    <h3>Work Done by Marius</h3>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
        <?php
                $totalMarius = 0;
                $sql = "SELECT * FROM work_done WHERE name = 'Marius'";
                if ($result = $conn->query($sql)) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['description'] . "</td>";
                        echo "<td>" . $row['amount'] . "</td>";
                        echo "</tr>";
                        $totalMarius += $row['amount'];
                    }
                    echo "<tr>";
                    echo "<td colspan='2'><strong>Total</strong></td>";
                    echo "<td><strong>$totalMarius</strong></td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
    <?php
        $sql = "SELECT amount FROM work_done";
        $total = 0;
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $total += $row['amount'];
        }
        echo "<td><strong>Total amount: $total</strong></td>";
        $conn->close();
    ?>
</body>
</html>
