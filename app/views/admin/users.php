<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-person-gear me-2"></i>จัดการผู้ใช้</h2>
    <a href="<?= BASE_URL ?>/admin" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>กลับ
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>ชื่อ</th>
                        <th>อีเมล</th>
                        <th>Role</th>
                        <th>สมัครเมื่อ</th>
                        <th class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $u): ?>
                    <?php $isSelf = ($u['id'] == $currentUser['id']); ?>
                    <tr>
                        <td class="text-muted small"><?= $u['id'] ?></td>
                        <td>
                            <?= htmlspecialchars($u['name']) ?>
                            <?php if ($isSelf): ?><span class="badge bg-secondary ms-1">คุณ</span><?php endif; ?>
                        </td>
                        <td class="text-muted small"><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <?php
                            $rc = ['admin'=>'danger','user'=>'success','pending'=>'warning'];
                            $rl = ['admin'=>'Admin','user'=>'User','pending'=>'Pending'];
                            ?>
                            <span class="badge bg-<?= $rc[$u['role']] ?? 'secondary' ?>">
                                <?= $rl[$u['role']] ?? $u['role'] ?>
                            </span>
                        </td>
                        <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($u['created_at'])) ?></td>
                        <td class="text-center">
                            <?php if (!$isSelf): ?>
                            <div class="d-flex justify-content-center gap-1">
                                <!-- Change Role -->
                                <form method="POST" action="<?= BASE_URL ?>/admin/role" class="d-flex gap-1 align-items-center">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <select name="role" class="form-select form-select-sm" style="width:auto">
                                        <option value="user"    <?= $u['role']==='user'    ? 'selected':'' ?>>User</option>
                                        <option value="admin"   <?= $u['role']==='admin'   ? 'selected':'' ?>>Admin</option>
                                        <option value="pending" <?= $u['role']==='pending' ? 'selected':'' ?>>Pending</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary" title="บันทึก Role">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <!-- Delete -->
                                <form method="POST" action="<?= BASE_URL ?>/admin/delete-user"
                                      onsubmit="return confirm('ลบผู้ใช้ <?= htmlspecialchars(addslashes($u['name'])) ?> ?')">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="ลบ">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                            <?php else: ?>
                            <span class="text-muted small">–</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">ยังไม่มีผู้ใช้</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
