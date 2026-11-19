/* jshint ignore:start */
/* eslint-disable */
/**
 * Item Intake - Part 3: IT Assets Intake
 * Handles: IT Assets Management UI and multi-item integration
 */

(function () {
    if (typeof ItemIntakeManager === "undefined") {
        console.error("ItemIntakeManager base class not found. Ensure item-intake.js is loaded first.");
        return;
    }

    ItemIntakeManager.prototype.applyItAssetsUI = function () {
        const standardFields = document.getElementById("standard-item-fields");
        const ddSection = document.getElementById("data-destruction-items-section");
        const headerText = document.getElementById("dd-items-header-text");
        const headerIcon = document.getElementById("dd-items-header-icon");
        const badge = document.getElementById("dd-items-count-badge");

        if (standardFields) standardFields.classList.add("hidden");
        if (ddSection) ddSection.classList.remove("hidden");

        if (headerText) headerText.textContent = "IT Asset Management";
        if (headerIcon) headerIcon.className = "fas fa-desktop text-blue-600";
        if (badge) badge.className = "bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-xs font-black";

        this.ddItems = [{ name: "", brand: "", model: "", serial: "" }];
        if (typeof this.renderDataDestructionItems === "function") {
            this.renderDataDestructionItems();
        }

        const ddQtyInput = document.querySelector('input[name="quantity"]');
        if (ddQtyInput) {
            ddQtyInput.value = "";
            ddQtyInput.readOnly = false;
            ddQtyInput.style.backgroundColor = "";
        }
    };
})();
/* jshint ignore:end */
/* eslint-enable */
