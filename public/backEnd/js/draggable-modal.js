/**
 * draggable-modal.js  — Bootstrap 5
 * ─────────────────────────────────
 * Add class "modal-draggable" to any .modal element and it becomes
 * freely draggable anywhere on screen.
 *
 * Usage:
 *   <div class="modal modal-draggable fade" id="myModal" ...>
 *
 * Works with: side-panel, centered, scrollable, any size, touch screens.
 * No jQuery required. No external libraries.
 */
(function () {
    'use strict';

    // ─── Core ────────────────────────────────────────────────────────────────

    /**
     * Attach drag behaviour to a .modal-dialog once the modal is fully shown.
     * We switch the dialog to position:fixed at its current screen coordinates
     * so we bypass Bootstrap's flex/margin centering entirely.
     */
    function initDraggable(modalEl) {
        const dialog = modalEl.querySelector('.modal-dialog');
        if (!dialog) return;

        // Re-init every time modal opens (position is reset on hide)
        dialog._dragging = false;

        // capture original inline style once
        if (dialog._originalStyle === undefined) {
            dialog._originalStyle = dialog.getAttribute('style') || '';
        }

        const handle = dialog.querySelector('.modal-header');
        if (!handle) return;

        // If already bound, skip re-binding listeners
        if (dialog._draggableBound) return;
        dialog._draggableBound = true;

        handle.style.cursor     = 'grab';
        handle.style.userSelect = 'none';

        let offsetX = 0, offsetY = 0;

        /* ── Utilities ─────────────────────────────────────────────── */

        function getCoords(e) {
            const src = e.touches ? e.touches[0] : e;
            return { x: src.clientX, y: src.clientY };
        }

        function fix(el) {
            const r        = el.getBoundingClientRect();
            el.style.position = 'fixed';
            el.style.margin   = '0';
            el.style.top      = r.top  + 'px';
            el.style.left     = r.left + 'px';
            el.style.width    = r.width + 'px';
            // prevent Bootstrap from overriding width with max-width
            el.style.maxWidth = 'none';
        }

        /* ── Drag handlers ─────────────────────────────────────────── */

        function onDown(e) {
            // Primary button / first touch only
            if (e.type === 'mousedown' && e.button !== 0) return;

            // Let clicks on interactive elements pass through
            if (e.target.closest('button, a, input, select, textarea, label, [data-bs-dismiss]')) return;

            // Lock dialog at current visual position
            fix(dialog);

            const { x, y } = getCoords(e);
            const r         = dialog.getBoundingClientRect();
            offsetX         = x - r.left;
            offsetY         = y - r.top;

            dialog._dragging      = true;
            handle.style.cursor   = 'grabbing';

            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup',   onUp);
            document.addEventListener('touchmove', onMove, { passive: false });
            document.addEventListener('touchend',  onUp);

            e.preventDefault();
        }

        function onMove(e) {
            if (!dialog._dragging) return;
            if (e.cancelable) e.preventDefault();

            const { x, y } = getCoords(e);

            const dW = dialog.offsetWidth;
            const dH = dialog.offsetHeight;
            const vW = window.innerWidth;
            const vH = window.innerHeight;
            const MARGIN = 20; // minimum visible pixels on each edge

            const newLeft = Math.min(Math.max(x - offsetX, MARGIN - dW), vW - MARGIN);
            const newTop  = Math.min(Math.max(y - offsetY, 0),           vH - MARGIN);

            dialog.style.left = newLeft + 'px';
            dialog.style.top  = newTop  + 'px';
        }

        function onUp() {
            if (!dialog._dragging) return;
            dialog._dragging    = false;
            dialog._wasDragged  = true; // remember that a drag occurred
            handle.style.cursor = 'grab';

            document.removeEventListener('mousemove', onMove);
            document.removeEventListener('mouseup',   onUp);
            document.removeEventListener('touchmove', onMove);
            document.removeEventListener('touchend',  onUp);
        }

        handle.addEventListener('mousedown',  onDown);
        handle.addEventListener('touchstart', onDown, { passive: false });
    }

    /**
     * Restore the dialog to its default (Bootstrap-managed) position
     * so it opens centred/positioned correctly next time.
     */
    function resetPosition(modalEl) {
        const dialog = modalEl.querySelector('.modal-dialog');
        if (!dialog) return;
        // always restore original inline style (captured on first init)
        if (dialog._originalStyle !== undefined) {
            dialog.style.cssText = dialog._originalStyle;
        }
        // clear drag state for next open
        dialog._dragging = false;
        dialog._wasDragged = false;
    }

    // ─── Bootstrap hooks ─────────────────────────────────────────────────────

    // Use "shown" (not "show") — dialog must be rendered & visible first
    document.addEventListener('shown.bs.modal', function (e) {
        if (e.target.classList.contains('modal-draggable')) {
            initDraggable(e.target);
        }
    });

    document.addEventListener('hidden.bs.modal', function (e) {
        if (e.target.classList.contains('modal-draggable')) {
            resetPosition(e.target);
        }
    });

})();



(function () {
    "use strict";

    /**
     * Add modal-draggable class if missing
     */
    function applyDraggableClass(root = document) {
        const modals = root.querySelectorAll('.modal');

        modals.forEach(function (modal) {
            if (!modal.classList.contains('modal-draggable')) {
                modal.classList.add('modal-draggable');
            }
        });
    }

    // Run on initial page load
    document.addEventListener('DOMContentLoaded', function () {
        applyDraggableClass();
    });

    /**
     * Observe DOM for dynamically added modals
     * (AJAX / Live rendering safe)
     */
    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            mutation.addedNodes.forEach(function (node) {

                // Only check element nodes
                if (node.nodeType !== 1) return;

                // If modal itself added
                if (node.classList && node.classList.contains('modal')) {
                    applyDraggableClass(node.parentNode);
                }

                // If modal exists inside added content
                if (node.querySelectorAll) {
                    applyDraggableClass(node);
                }
            });
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

})();
