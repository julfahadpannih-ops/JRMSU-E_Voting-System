<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>JRMSU SSG Election Results</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1a1a2e; font-size: 13px; background: #fff; }
        .header { background: #001f3f; color: white; padding: 28px 40px 20px; text-align: center; }
        .header h1 { font-size: 22px; font-weight: 800; letter-spacing: 0.05em; margin-bottom: 4px; }
        .header p  { font-size: 12px; color: rgba(255,255,255,0.7); }
        .header .badge { display: inline-block; margin-top: 10px; background: #ffc107; color: #001f3f; font-weight: 700; font-size: 11px; padding: 3px 14px; border-radius: 999px; }
        .content { padding: 30px 40px; }
        .position-block { margin-bottom: 30px; }
        .position-title { font-size: 15px; font-weight: 700; color: #001f3f; border-left: 4px solid #ffc107; padding-left: 10px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        th { background: #f1f5f9; text-align: left; padding: 8px 12px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: #475569; }
        td { padding: 9px 12px; border-bottom: 1px solid #e2e8f0; font-size: 12px; }
        tr:last-child td { border-bottom: none; }
        .winner { font-weight: 700; color: #15803d; }
        .rank-1 td:first-child::before { content: '🥇 '; }
        .rank-2 td:first-child::before { content: '🥈 '; }
        .rank-3 td:first-child::before { content: '🥉 '; }
        .votes-bar { display: inline-block; height: 8px; background: #ffc107; border-radius: 4px; min-width: 4px; }
        .footer { text-align: center; color: #94a3b8; font-size: 10px; padding: 20px 40px 30px; border-top: 1px solid #e2e8f0; }
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<div class="no-print" style="background:#001f3f;color:white;padding:10px 20px;display:flex;gap:10px;align-items:center;">
    <span style="font-size:13px;">📄 Print this page to save as PDF (Ctrl+P / Cmd+P)</span>
    <button onclick="window.print()" style="background:#ffc107;color:#001f3f;border:none;padding:6px 18px;border-radius:6px;font-weight:700;cursor:pointer;">🖨️ Print / Save PDF</button>
    <a href="<?php echo e(route('admin.results.index')); ?>" style="color:rgba(255,255,255,0.7);font-size:12px;margin-left:10px;">← Back to Results</a>
</div>

<div class="header">
    <h1>JRMSU Siocon Campus — SSG Election</h1>
    <p>J.H. Cerilles State College — Siocon Campus</p>
    <div class="badge">Official Election Results</div>
    <p style="margin-top:8px; font-size:11px; color:rgba(255,255,255,0.6);">Exported: <?php echo e($exportedAt); ?></p>
</div>

<div class="content">
    <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $positionName => $candidates): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $maxVotes = $candidates->max('voteCount'); ?>
    <div class="position-block">
        <div class="position-title"><?php echo e($positionName); ?></div>
        <table>
            <thead>
                <tr>
                    <th>Candidate</th>
                    <th>Party List</th>
                    <th>Votes</th>
                    <th style="width:200px">Distribution</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $candidates->sortByDesc('voteCount')->values(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="rank-<?php echo e($i + 1); ?> <?php echo e($i === 0 && $c['voteCount'] > 0 ? 'winner' : ''); ?>">
                    <td><?php echo e($c['candidateName']); ?></td>
                    <td><?php echo e($c['partyList'] ?? '—'); ?></td>
                    <td><?php echo e($c['voteCount']); ?></td>
                    <td>
                        <?php if($maxVotes > 0): ?>
                        <span class="votes-bar" style="width:<?php echo e(round(($c['voteCount'] / $maxVotes) * 160)); ?>px"></span>
                        <?php endif; ?>
                        <span style="margin-left:6px;font-size:11px;color:#64748b;">
                            <?php echo e($maxVotes > 0 ? round(($c['voteCount'] / $maxVotes) * 100) : 0); ?>%
                        </span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="footer">
    This document is an official export from the JRMSU Siocon SSG E-Voting System. &nbsp;|&nbsp; Generated: <?php echo e($exportedAt); ?>

</div>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\JRMSU E_VOTING SYSTEM\resources\views/admin/results-pdf.blade.php ENDPATH**/ ?>