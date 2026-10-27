<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Barcode Labels</title>
<link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

body {
  background: #ddd;
  font-family: Arial, sans-serif;
  padding: 24px;
}

/* Controls bar — hidden on print */
.bc-topbar {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
  padding: 10px 16px;
  background: #fff;
  border-radius: 6px;
  box-shadow: 0 1px 4px rgba(0,0,0,.15);
}
.bc-topbar button {
  padding: 6px 16px;
  border: 1px solid #ccc;
  border-radius: 4px;
  cursor: pointer;
  font-size: 13px;
  background: #fff;
}
.bc-topbar .btn-print {
  background: #337ab7;
  color: #fff;
  border-color: #2e6da4;
}
.bc-count {
  font-size: 13px;
  color: #666;
}

/* Label stack */
.bc-grid {
  display: flex;
  flex-direction: column;
  gap: 20px;
  align-items: flex-start;
}

/* Screen preview: 3× physical size (1.69in × 1.25in) */
.bc-label {
  width: 486px;
  height: 360px;
  border: 4px solid #111;
  border-radius: 28px;
  padding: 20px 22px 18px;
  background: #fff;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  page-break-inside: avoid;
  break-inside: avoid;
}

.bc-company {
  font-size: 15px;
  font-weight: 400;
  color: #444;
  line-height: 1.4;
  margin-bottom: 3px;
}
.bc-product {
  font-size: 21px;
  font-weight: 700;
  color: #000;
  line-height: 1.3;
  margin-bottom: 3px;
  word-break: break-word;
}
.bc-price {
  font-size: 40px;
  font-weight: 900;
  color: #000;
  line-height: 1.2;
  letter-spacing: -0.5px;
  margin-bottom: 8px;
}
.bc-barcode-img {
  width: 430px;
  height: 100px;
  display: block;
  margin-bottom: 5px;
}
.bc-code {
  font-family: 'Share Tech Mono', 'Courier New', Courier, monospace;
  font-size: 17px;
  color: #000;
  letter-spacing: 0.14em;
  margin-bottom: 3px;
}
.bc-batch {
  font-size: 14px;
  color: #333;
}

.bc-empty {
  padding: 40px;
  background: #fff;
  border-radius: 6px;
  color: #888;
  font-size: 15px;
}

/* ===== Print ===== */
@media print {
  body {
    background: white;
    padding: 0;
    margin: 0;
  }
  .bc-topbar { display: none; }

  .bc-grid {
    gap: 6mm;
  }

  /* Exact physical size: 1.69in × 1.25in */
  .bc-label {
    width: 1.69in;
    height: 1.25in;
    border: 1.5pt solid #000;
    border-radius: 5pt;
    padding: 5pt 6pt 4pt;
    overflow: hidden;
  }

  .bc-company      { font-size: 6pt;   margin-bottom: 1pt; }
  .bc-product      { font-size: 8pt;   font-weight: 700; margin-bottom: 1pt; }
  .bc-price        { font-size: 14pt;  font-weight: 900; margin-bottom: 2pt; }
  .bc-barcode-img  { width: 1.45in; height: 0.40in; margin-bottom: 1pt; }
  .bc-code         { font-size: 6pt;   letter-spacing: 0.12em; margin-bottom: 0.5pt; }
  .bc-batch        { font-size: 5.5pt; }
}
</style>
</head>
<body>

<!-- Controls (hidden on print) -->
<div class="bc-topbar">
  <button class="btn-print" onclick="window.print()">&#128438; Print Labels</button>
  <button onclick="window.close()">&#8592; Close</button>
  <span class="bc-count"><?php echo count($stickers); ?> label<?php echo count($stickers) !== 1 ? 's' : ''; ?> ready</span>
</div>

<!-- Labels -->
<?php if (empty($stickers)): ?>
  <div class="bc-empty">No labels found. Please go back and select products.</div>
<?php else: ?>
<div class="bc-grid">
  <?php foreach ($stickers as $s):
    $pid           = $s['product_id'];
    $price_val     = number_format((float)$s['price'], 2, '.', ',');
    $price_display = ($position == 0)
        ? htmlspecialchars($currency) . '&nbsp;' . $price_val
        : $price_val . '&nbsp;' . htmlspecialchars($currency);
  ?>
  <div class="bc-label">
    <div class="bc-company"><?php echo htmlspecialchars($company_name); ?></div>
    <div class="bc-product"><?php echo htmlspecialchars($s['product_name']); ?></div>
    <div class="bc-price"><?php echo $price_display; ?></div>
    <img class="bc-barcode-img"
         src="<?php echo base_url('vendor/barcode.php?size=70&text=' . urlencode($pid)); ?>"
         alt="<?php echo htmlspecialchars($pid); ?>">
    <div class="bc-code"><?php echo htmlspecialchars($pid); ?></div>
    <?php if (!empty($s['batch_name'])): ?>
    <div class="bc-batch">Batch ID: <?php echo htmlspecialchars($s['batch_name']); ?></div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
// Auto-trigger print once all images have loaded
window.addEventListener('load', function () {
    window.print();
});
</script>
</body>
</html>
