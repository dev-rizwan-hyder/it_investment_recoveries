{{-- SQUARE PAYMENT REMINDER MODAL --}}
<div id="squareRemindModal" class="hidden fixed inset-0 z-[150] items-center justify-center p-4" style="display: none;">
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closeSquareRemindModal()"></div>
    <div class="relative flex w-full max-w-2xl flex-col overflow-hidden rounded-3xl bg-white shadow-2xl animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/80 px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 shadow-sm">
                    <i class="fas fa-bell text-sm"></i>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-800">Send Payment Reminder</h3>
                    <p class="text-xs font-semibold text-slate-500">
                        Invoice <span id="squareRemindInvoiceNum" class="font-mono font-bold text-blue-600"></span> &bull; Square Hosted Payment
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeSquareRemindModal()"
                class="flex h-9 w-9 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-200/60 hover:text-slate-600">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="squareRemindForm" method="POST" action="" class="p-6 space-y-5">
            @csrf

            <!-- Invoice & Recipient Details Card -->
            <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-4 space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="block text-[10px] font-black uppercase tracking-wider text-slate-400">Customer</span>
                        <span id="squareRemindCustomerName" class="block font-bold text-slate-800 text-sm truncate mt-0.5"></span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-black uppercase tracking-wider text-slate-400">Total Balance</span>
                        <span id="squareRemindAmount" class="block font-black text-emerald-600 text-base mt-0.5"></span>
                    </div>
                </div>

                <div class="border-t border-slate-200/60 pt-3">
                    <label for="squareRemindEmail" class="block text-[10px] font-black uppercase tracking-wider text-slate-500 mb-1">
                        Recipient Email Address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="email" id="squareRemindEmail" name="recipient_email" required
                            class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-9 pr-3 text-xs font-bold text-slate-800 outline-none transition focus:border-blue-400 focus:ring-4 focus:ring-blue-500/10">
                    </div>
                </div>

                <!-- Previous reminder status badge -->
                <div id="squareRemindHistoryRow" class="hidden pt-1 flex items-center justify-between text-[11px] font-semibold text-slate-500">
                    <span class="flex items-center gap-1.5 text-amber-700">
                        <i class="fas fa-history text-xs"></i>
                        <span id="squareRemindHistoryText"></span>
                    </span>
                    <span id="squareRemindDueDate" class="text-slate-400"></span>
                </div>
            </div>

            <!-- Message Presets & Textarea -->
            <div>
                <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                    <label for="squareRemindMessage" class="text-xs font-black uppercase tracking-wide text-slate-700">
                        Reminder Message & Text <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-1.5 text-[11px]">
                        <span class="text-slate-400 font-bold">Presets:</span>
                        <button type="button" onclick="setSquareRemindPreset('friendly')"
                            class="rounded-lg bg-blue-50 px-2 py-0.5 font-bold text-blue-600 hover:bg-blue-100 transition">
                            Friendly
                        </button>
                        <button type="button" onclick="setSquareRemindPreset('urgent')"
                            class="rounded-lg bg-amber-50 px-2 py-0.5 font-bold text-amber-700 hover:bg-amber-100 transition">
                            Past Due
                        </button>
                        <button type="button" onclick="setSquareRemindPreset('short')"
                            class="rounded-lg bg-slate-100 px-2 py-0.5 font-bold text-slate-600 hover:bg-slate-200 transition">
                            Short
                        </button>
                    </div>
                </div>

                <div class="relative">
                    <textarea id="squareRemindMessage" name="message" rows="6" required maxlength="2000"
                        class="w-full rounded-2xl border border-slate-200 bg-white p-4 text-xs font-medium text-slate-800 leading-relaxed outline-none transition focus:border-blue-400 focus:ring-4 focus:ring-blue-500/10"></textarea>
                </div>
                <p class="mt-1.5 text-[11px] font-semibold text-slate-400 leading-relaxed">
                    This message will be sent in a professional payment reminder email containing an instant <strong>Pay with Square</strong> online link.
                </p>
            </div>

            <!-- Footer / Action Buttons -->
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeSquareRemindModal()"
                    class="rounded-xl bg-slate-100 px-5 py-3 text-xs font-black text-slate-600 transition hover:bg-slate-200">
                    Cancel
                </button>
                <button type="submit" id="squareRemindSubmitBtn"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-6 py-3 text-xs font-black text-white shadow-lg shadow-amber-500/20 transition hover:bg-amber-600 active:scale-95 disabled:opacity-50">
                    <i class="fas fa-paper-plane text-xs"></i>
                    <span>Send Reminder Now</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentSquareRemindData = null;

    function decodeBase64Utf8(str) {
        if (!str) return '';
        try {
            const binString = atob(str);
            const bytes = Uint8Array.from(binString, (m) => m.charCodeAt(0));
            return new TextDecoder().decode(bytes);
        } catch (e) {
            try {
                return decodeURIComponent(escape(atob(str)));
            } catch (err) {
                return '';
            }
        }
    }

    function triggerSquareRemind(btn) {
        if (!btn) return;
        const b64Msg = btn.getAttribute('data-message-b64') || '';
        const defaultMsg = decodeBase64Utf8(b64Msg);

        openSquareRemindModal({
            url: btn.getAttribute('data-url') || '',
            invoiceNumber: btn.getAttribute('data-invoice-number') || '',
            customerName: btn.getAttribute('data-customer-name') || '',
            customerEmail: btn.getAttribute('data-customer-email') || '',
            amount: btn.getAttribute('data-amount') || '$0.00',
            dueDate: btn.getAttribute('data-due-date') || 'Upon receipt',
            hasSquare: btn.getAttribute('data-has-square') === '1',
            squareUrl: btn.getAttribute('data-square-url') || '',
            reminderCount: parseInt(btn.getAttribute('data-reminder-count') || '0', 10),
            lastRemindedAt: btn.getAttribute('data-last-reminded') || '',
            defaultMessage: defaultMsg
        });
    }

    function openSquareRemindModal(data) {
        data = data || {};
        currentSquareRemindData = data;
        const modal = document.getElementById('squareRemindModal');
        if (!modal) {
            console.error('squareRemindModal element not found!');
            return;
        }

        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        const form = document.getElementById('squareRemindForm');
        const invoiceNum = document.getElementById('squareRemindInvoiceNum');
        const customerName = document.getElementById('squareRemindCustomerName');
        const amount = document.getElementById('squareRemindAmount');
        const emailInput = document.getElementById('squareRemindEmail');
        const messageInput = document.getElementById('squareRemindMessage');
        const historyRow = document.getElementById('squareRemindHistoryRow');
        const historyText = document.getElementById('squareRemindHistoryText');
        const dueDate = document.getElementById('squareRemindDueDate');
        const submitBtn = document.getElementById('squareRemindSubmitBtn');

        form.action = data.url || '';
        invoiceNum.textContent = data.invoiceNumber || '';
        customerName.textContent = data.customerName || 'Valued Customer';
        amount.textContent = data.amount || '$0.00';
        emailInput.value = data.customerEmail || '';

        let message = data.defaultMessage || '';
        if (!message) {
            const name = data.customerName || 'Valued Customer';
            const num = data.invoiceNumber || '';
            const amt = data.amount || '$0.00';
            const due = data.dueDate || 'upon receipt';
            message = `Hi ${name},\n\nThis is a friendly reminder from IT INVESTMENT RECOVERIES regarding invoice #${num} for ${amt}, which is due ${due}.\n\nPlease review your invoice and submit payment securely online using the Square payment link provided.\n\nThank you for choosing IT INVESTMENT RECOVERIES!`;
        }
        messageInput.value = message;

        if (data.reminderCount && data.reminderCount > 0) {
            historyRow.classList.remove('hidden');
            historyText.textContent = `${data.reminderCount} reminder${data.reminderCount > 1 ? 's' : ''} sent previously` +
                (data.lastRemindedAt ? ` (last: ${data.lastRemindedAt})` : '');
        } else {
            historyRow.classList.add('hidden');
        }

        if (dueDate) {
            dueDate.textContent = data.dueDate ? `Due Date: ${data.dueDate}` : '';
        }

        // Reset button
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane text-xs"></i> <span>Send Reminder Now</span>';

        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            messageInput.focus();
        }, 100);
    }

    function closeSquareRemindModal() {
        const modal = document.getElementById('squareRemindModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
        document.body.style.overflow = '';
    }

    function setSquareRemindPreset(preset) {
        if (!currentSquareRemindData) return;
        const msgInput = document.getElementById('squareRemindMessage');
        const name = currentSquareRemindData.customerName || 'Valued Customer';
        const num = currentSquareRemindData.invoiceNumber || 'your invoice';
        const amt = currentSquareRemindData.amount || 'the balance';
        const due = currentSquareRemindData.dueDate || 'upon receipt';

        if (preset === 'friendly') {
            msgInput.value = `Hi ${name},\n\nThis is a friendly reminder from IT INVESTMENT RECOVERIES regarding invoice #${num} for ${amt}, which is due ${due}.\n\nPlease review your invoice and submit payment securely online using the Square payment link provided.\n\nThank you for choosing IT INVESTMENT RECOVERIES!`;
        } else if (preset === 'urgent') {
            msgInput.value = `Dear ${name},\n\nOur records indicate that invoice #${num} for ${amt} was due on ${due} and currently remains outstanding.\n\nPlease take a moment to submit your payment online through the secure Square payment link provided below.\n\nIf you have already submitted payment or have any questions, please contact us immediately.\n\nThank you,\nIT INVESTMENT RECOVERIES`;
        } else if (preset === 'short') {
            msgInput.value = `Hi ${name}, quick reminder that invoice #${num} (${amt}) is ready for payment. You can pay securely online via Square using the link below. Thank you!`;
        }
    }

    window.triggerSquareRemind = triggerSquareRemind;
    window.openSquareRemindModal = openSquareRemindModal;
    window.closeSquareRemindModal = closeSquareRemindModal;
    window.setSquareRemindPreset = setSquareRemindPreset;

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('squareRemindForm');
        if (form) {
            form.addEventListener('submit', () => {
                const btn = document.getElementById('squareRemindSubmitBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin text-xs"></i> <span>Sending Reminder...</span>';
                }
            });
        }

        document.addEventListener('click', (event) => {
            const btn = event.target.closest('[data-square-remind-btn]');
            if (btn) {
                event.preventDefault();
                triggerSquareRemind(btn);
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                const modal = document.getElementById('squareRemindModal');
                if (modal && (!modal.classList.contains('hidden') || modal.style.display !== 'none')) {
                    closeSquareRemindModal();
                }
            }
        });
    });
</script>
