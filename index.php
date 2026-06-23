<?php
$voltage = "";
$current = "";
$rate = "";
$powerKW = 0;
$rateRM = 0;
$dailyEnergy = 0;
$dailyCost = 0;
$results = [];
$error = "";

function calculatePower($voltage, $current) {
    return ($voltage * $current) / 1000;
}

function calculateEnergy($powerKW, $hour) {
    return $powerKW * $hour;
}

function calculateTotalCost($energy, $rate) {
    $rateRM = $rate / 100;
    return $energy * $rateRM;
}

function calculateElectricityRates($voltage, $current, $rate) {
    $powerKW = calculatePower($voltage, $current);
    $results = [];

    for ($hour = 1; $hour <= 24; $hour++) {
        $energy = calculateEnergy($powerKW, $hour);
        $total = calculateTotalCost($energy, $rate);

        $results[] = [
            "hour" => $hour,
            "energy" => $energy,
            "total" => $total
        ];
    }

    return $results;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $voltage = $_POST["voltage"];
    $current = $_POST["current"];
    $rate = $_POST["rate"];

    if ($voltage <= 0 || $current <= 0 || $rate <= 0) {
        $error = "Please enter valid positive values.";
    } else {
        $powerKW = calculatePower($voltage, $current);
        $rateRM = $rate / 100;
        $results = calculateElectricityRates($voltage, $current, $rate);

        $dailyEnergy = calculateEnergy($powerKW, 24);
        $dailyCost = calculateTotalCost($dailyEnergy, $rate);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Electricity Calculator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5 mb-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h2>Electricity Consumption Calculator</h2>
            <p class="mb-0">Calculate power, energy consumption, and estimated electricity cost.</p>
        </div>

        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Voltage (V)</label>
                    <input type="number" step="0.01" name="voltage" class="form-control"
                           placeholder="Example: 19" value="<?= htmlspecialchars($voltage) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Current (A)</label>
                    <input type="number" step="0.01" name="current" class="form-control"
                           placeholder="Example: 3.24" value="<?= htmlspecialchars($current) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Current Rate (sen/kWh)</label>
                    <input type="number" step="0.01" name="rate" class="form-control"
                           placeholder="Example: 21.80" value="<?= htmlspecialchars($rate) ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">Calculate</button>
                <a href="index.php" class="btn btn-secondary">Reset</a>
            </form>
        </div>
    </div>

    <?php if ($error != ""): ?>
        <div class="alert alert-danger mt-4">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($results)): ?>
        <div class="card shadow mt-4">
            <div class="card-body">
                <h4>Calculation Summary</h4>

                <div class="row">
                    <div class="col-md-3">
                        <div class="border rounded p-3 mb-3">
                            <strong>Power</strong>
                            <p class="mb-0"><?= number_format($powerKW, 5) ?> kW</p>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded p-3 mb-3">
                            <strong>Rate</strong>
                            <p class="mb-0">RM <?= number_format($rateRM, 3) ?> / kWh</p>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded p-3 mb-3">
                            <strong>24 Hours Energy</strong>
                            <p class="mb-0"><?= number_format($dailyEnergy, 5) ?> kWh</p>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded p-3 mb-3">
                            <strong>24 Hours Cost</strong>
                            <p class="mb-0">RM <?= number_format($dailyCost, 2) ?></p>
                        </div>
                    </div>
                </div>

                <table class="table table-bordered table-striped mt-4">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Hour</th>
                            <th>Energy Consumption (kWh)</th>
                            <th>Estimated Cost (RM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $index => $row): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $row["hour"] ?></td>
                                <td><?= number_format($row["energy"], 5) ?></td>
                                <td><?= number_format($row["total"], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <p class="text-center text-muted mt-4">
        Developed by Anis Najwa Binti Kamarul Ariffin
    </p>
</div>

</body>
</html>
