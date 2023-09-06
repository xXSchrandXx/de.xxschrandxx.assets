import * as Core from "WoltLabSuite/Core/Core";
import AbstractAssetAction from "./Abstract";
import AuditHandler from "./Handler/Audit";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";

export class AuditAction extends AbstractAssetAction {
    private auditHandler: AuditHandler;

    public constructor(button: HTMLElement, assetId: number, assetDataElement: HTMLElement) {
        super(button, assetId, assetDataElement);

        this.auditHandler = new AuditHandler([this.assetId]);

        this.button.addEventListener("click", (event) => {
            event.preventDefault();

            if (this.button.hidden) {
                throw Error("No permission!");
            }

            const isTrashed = Core.stringToBool(this.assetDataElement.dataset.trashed!);

            if (!isTrashed) {
                this.auditHandler.audit(() => {
                    UiNotification.show();
                });
            } else {
                throw Error("Asset is trashed!");
            }
        });
    }
}

export default AuditAction;