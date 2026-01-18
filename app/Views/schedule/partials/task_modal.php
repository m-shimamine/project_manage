<!-- タスク編集モーダル -->
<div id="task-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeTaskModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 id="modal-title" class="text-lg font-bold text-white">
                    <i class="fas fa-tasks mr-2"></i>新規タスク
                </h3>
                <button onclick="closeTaskModal()" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <form id="task-form" onsubmit="event.preventDefault(); saveTask();">
            <input type="hidden" id="task-id" name="id" value="">
            <input type="hidden" id="task-project-id" name="project_id" value="">

            <div class="p-6 overflow-y-auto max-h-[60vh]">
                <div class="grid grid-cols-2 gap-4">
                    <!-- タスク名 -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            タスク名 <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="task-name" name="task_name" required
                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="タスク名を入力">
                    </div>

                    <!-- 工程 -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            工程 <span class="text-red-500">*</span>
                        </label>
                        <select id="task-process" name="process_id" required
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">選択してください</option>
                            <?php foreach ($processes as $process): ?>
                                <option value="<?= $process['id'] ?>"><?= esc($process['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- 担当者 -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">担当者</label>
                        <select id="task-assignee" name="assignee_id"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">未割当</option>
                            <?php foreach ($members as $member): ?>
                                <option value="<?= $member['id'] ?>"><?= esc($member['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- ステータス -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">ステータス</label>
                        <select id="task-status" name="status"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="not_started">未着手</option>
                            <option value="in_progress">進行中</option>
                            <option value="completed">完了</option>
                            <option value="on_hold">保留</option>
                        </select>
                    </div>

                    <!-- 進捗率 -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">進捗率 (%)</label>
                        <input type="number" id="task-progress" name="progress" min="0" max="100" value="0"
                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- 区切り線 -->
                    <div class="col-span-2 border-t border-slate-200 my-2"></div>

                    <div class="col-span-2">
                        <h4 class="text-sm font-semibold text-slate-600 mb-3">
                            <i class="fas fa-calendar-alt mr-1 text-blue-500"></i>予定
                        </h4>
                    </div>

                    <!-- 予定開始日 -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">予定開始日</label>
                        <input type="date" id="task-planned-start" name="planned_start_date"
                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- 予定終了日 -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">予定終了日</label>
                        <input type="date" id="task-planned-end" name="planned_end_date"
                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- 予定工数 -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">予定工数（人日）</label>
                        <input type="number" id="task-planned-man-days" name="planned_man_days" min="0" step="0.5"
                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- 営業工数 -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">営業工数（人日）</label>
                        <input type="number" id="task-sales-man-days" name="sales_man_days" min="0" step="0.5"
                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- 予定原価 -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">予定原価（円）</label>
                        <input type="number" id="task-planned-cost" name="planned_cost" min="0"
                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- 区切り線 -->
                    <div class="col-span-2 border-t border-slate-200 my-2"></div>

                    <!-- 備考 -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">備考</label>
                        <textarea id="task-description" name="description" rows="3"
                                  class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="メモや備考を入力"></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex justify-between">
                <div>
                    <button type="button" onclick="deleteTask()" id="btn-delete-task"
                            class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors hidden">
                        <i class="fas fa-trash mr-1"></i>削除
                    </button>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeTaskModal()"
                            class="px-5 py-2.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                        キャンセル
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/25">
                        <i class="fas fa-save mr-1"></i>保存
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// 編集時に削除ボタンを表示
document.addEventListener('DOMContentLoaded', function() {
    const taskIdInput = document.getElementById('task-id');
    const deleteBtn = document.getElementById('btn-delete-task');

    if (taskIdInput && deleteBtn) {
        const observer = new MutationObserver(function() {
            deleteBtn.classList.toggle('hidden', !taskIdInput.value);
        });

        observer.observe(taskIdInput, { attributes: true, attributeFilter: ['value'] });

        // 初期状態
        taskIdInput.addEventListener('change', function() {
            deleteBtn.classList.toggle('hidden', !this.value);
        });
    }
});

// loadTaskData関数を拡張（削除ボタン表示用）
const originalLoadTaskData = window.loadTaskData;
window.loadTaskData = async function(taskId) {
    await originalLoadTaskData(taskId);
    document.getElementById('btn-delete-task').classList.remove('hidden');
};

// 新規作成時は削除ボタンを隠す
const originalOpenTaskModal = window.openTaskModal;
window.openTaskModal = function(taskId = null) {
    originalOpenTaskModal(taskId);
    document.getElementById('btn-delete-task').classList.toggle('hidden', !taskId);
};
</script>
