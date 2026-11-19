/* jshint ignore:start */
/* eslint-disable */
/**
 * Item Intake - Part 5: Universal Waste Intake
 * Handles: Universal Waste Intake workflow, validation, and recycling label handling
 */

(function () {
    if (typeof ItemIntakeManager === "undefined") {
        console.error("ItemIntakeManager base class not found. Ensure item-intake.js is loaded first.");
        return;
    }

    ItemIntakeManager.prototype.applyUniversalWasteUI = function () {
        const standardFields = document.getElementById("standard-item-fields");
        const ddSection = document.getElementById("data-destruction-items-section");
        const saveDraftBtn = document.getElementById("save-draft-item-btn");

        if (standardFields) standardFields.classList.remove("hidden");
        if (ddSection) ddSection.classList.add("hidden");
        this.ddItems = [];

        if (saveDraftBtn) {
            saveDraftBtn.classList.remove("hidden");
        }

        const qtyInput = document.querySelector('input[name="quantity"]');
        if (qtyInput) {
            qtyInput.value = "";
            qtyInput.readOnly = false;
            qtyInput.style.backgroundColor = "";
        }
    };
})();
/* jshint ignore:end */
/* eslint-enable */
