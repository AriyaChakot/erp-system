<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Payslip — <?= htmlspecialchars($slip['slip_number']) ?></title>
<style>
  body { font-family: 'Sarabun', sans-serif; font-size: 14px; color: #333; margin: 0; padding: 20px; }
  .slip-wrap { max-width: 680px; margin: 0 auto; border: 2px solid #dee2e6; padding: 32px; }
  .slip-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 2px solid #0d6efd; padding-bottom: 16px; }
  .company-name { font-size: 18px; font-weight: bold; color: #0d6efd; }
  .slip-title { font-size: 22px; font-weight: bold; text-align: right; }
  .slip-number { color: #666; font-size: 12px; }
  .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 16px; margin-bottom: 24px; background: #f8f9fa; padding: 16px; border-radius: 6px; }
  .info-item label { font-size: 11px; color: #666; display: block; }
  .info-item span { font-weight: 600; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
  th { background: #f0f4ff; padding: 8px 12px; text-align: left; font-size: 12px; color: #444; border-bottom: 2px solid #dee2e6; }
  td { padding: 7px 12px; border-bottom: 1px solid #f0f0f0; }
  .text-end { text-align: right; }
  .text-muted { color: #888; font-size: 12px; }
  .total-row { font-weight: bold; background: #f8f9fa; }
  .net-row { font-size: 16px; font-weight: bold; color: #0d6efd; background: #e8f0ff; }
  .footer { margin-top: 32px; display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
  .sign-box { text-align: center; padding-top: 40px; border-top: 1px dotted #999; font-size: 12px; color: #666; }
  @media print { .no-print { display: none; } body { padding: 0; } }
</style>
</head>
<body>
<div class="no-print" style="max-width:680px;margin:0 auto 16px;display:flex;gap:8px;justify-content:flex-end">
    <button onclick="window.print()" class="btn btn-primary btn-sm" style="padding:6px 16px;background:#0d6efd;color:#fff;border:none;border-radius:4px;cursor:pointer">
        🖨 พิมพ์ Payslip
    </button>
    <a href="javascript:history.back()" style="padding:6px 16px;background:#6c757d;color:#fff;border:none;border-radius:4px;text-decoration:none;font-size:14px">← ย้อนกลับ</a>
</div>

<div class="slip-wrap">
    <div class="slip-header">
        <div>
            <div class="company-name">บริษัท ตัวอย่าง จำกัด</div>
            <div class="text-muted">ใบรับรองเงินเดือน (Pay Slip)</div>
        </div>
        <div>
            <div class="slip-title">ใบรับรองเงินเดือน</div>
            <div class="slip-number"><?= htmlspecialchars($slip['slip_number']) ?></div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-item"><label>ชื่อพนักงาน</label><span><?= htmlspecialchars($slip['employee_name']) ?></span></div>
        <div class="info-item"><label>Period</label><span><?= htmlspecialchars($slip['period_name']) ?></span></div>
        <div class="info-item"><label>แผนก</label><span><?= htmlspecialchars($slip['department'] ?? '-') ?></span></div>
        <div class="info-item"><label>ตำแหน่ง</label><span><?= htmlspecialchars($slip['position'] ?? '-') ?></span></div>
        <div class="info-item"><label>วันที่จ่ายเงิน</label><span><?= date('d/m/Y', strtotime($slip['pay_date'])) ?></span></div>
        <div class="info-item"><label>วันทำงาน / วันลา</label><span><?= $slip['working_days'] ?> / <?= $slip['leave_days'] ?> วัน</span></div>
    </div>

    <!-- รายได้ -->
    <table>
        <thead><tr><th>รายได้</th><th class="text-end">จำนวนเงิน (บาท)</th></tr></thead>
        <tbody>
            <tr><td>เงินเดือนฐาน</td><td class="text-end">฿<?= number_format($slip['base_salary'], 2) ?></td></tr>
            <?php if ($slip['ot_amount'] > 0): ?>
            <tr><td>ค่าล่วงเวลา (<?= number_format($slip['ot_hours'], 1) ?> ชม.)</td><td class="text-end">฿<?= number_format($slip['ot_amount'], 2) ?></td></tr>
            <?php endif; ?>
            <?php if (!empty($slip['allowance']) && $slip['allowance'] > 0): ?>
            <tr><td>เบี้ยเลี้ยง</td><td class="text-end">฿<?= number_format($slip['allowance'], 2) ?></td></tr>
            <?php endif; ?>
            <?php if (!empty($slip['bonus']) && $slip['bonus'] > 0): ?>
            <tr><td>โบนัส</td><td class="text-end">฿<?= number_format($slip['bonus'], 2) ?></td></tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="total-row"><td>รวมรายได้ก่อนหัก</td><td class="text-end">฿<?= number_format($slip['gross_salary'], 2) ?></td></tr>
        </tfoot>
    </table>

    <!-- หักลด -->
    <table>
        <thead><tr><th>หักลด</th><th class="text-end">จำนวนเงิน (บาท)</th></tr></thead>
        <tbody>
            <tr><td>ประกันสังคม (5%, สูงสุด 750 บาท)</td><td class="text-end text-danger">-฿<?= number_format($slip['social_security'], 2) ?></td></tr>
            <tr><td>ภาษีเงินได้หัก ณ ที่จ่าย</td><td class="text-end text-danger">-฿<?= number_format($slip['income_tax'], 2) ?></td></tr>
            <?php if (!empty($slip['other_deductions']) && $slip['other_deductions'] > 0): ?>
            <tr><td>หักอื่นๆ</td><td class="text-end text-danger">-฿<?= number_format($slip['other_deductions'], 2) ?></td></tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="total-row"><td>รวมหักลด</td><td class="text-end text-danger">-฿<?= number_format($slip['total_deductions'], 2) ?></td></tr>
        </tfoot>
    </table>

    <!-- เงินสุทธิ -->
    <table>
        <tfoot>
            <tr class="net-row">
                <td>เงินได้สุทธิ (Net Pay)</td>
                <td class="text-end">฿<?= number_format($slip['net_salary'], 2) ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div class="sign-box">ลายมือชื่อพนักงาน<br><small><?= htmlspecialchars($slip['employee_name']) ?></small></div>
        <div class="sign-box">ลายมือชื่อผู้อนุมัติ<br><small>ฝ่ายทรัพยากรบุคคล</small></div>
    </div>

    <div class="text-muted text-center" style="margin-top:24px;font-size:11px">
        เอกสารนี้ออกโดยระบบ ERP — <?= date('d/m/Y H:i') ?>
    </div>
</div>
</body>
</html>
