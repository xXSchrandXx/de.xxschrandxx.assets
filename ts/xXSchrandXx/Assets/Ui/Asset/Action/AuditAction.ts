import * as Ajax from "WoltLabSuite/Core/Ajax";
import { confirmationFactory } from "WoltLabSuite/Core/Component/Confirmation";
import * as Core from "WoltLabSuite/Core/Core";
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";
import { getPhrase } from "WoltLabSuite/Core/Language";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";

export class AuditAction {
    public constructor(button: HTMLElement, assetId: number, assetDataElement: HTMLElement) {
        button.addEventListener("click", async (event) => {
            event.preventDefault();

            // check if action is available
            if (button.hidden) {
                throw Error("No permission!");
            }
            if (button.classList.contains("disabled")) {
                return;
            }
            button.classList.add("disabled");

            // check weather the asset is trashed
            const isTrashed = Core.stringToBool(assetDataElement.dataset.trashed!);
            if (isTrashed) {
                throw Error("Asset is trashed!");
            }

            const result = await confirmationFactory()
                .withReason(getPhrase("wcf.dialog.confirmation.audit", {title: assetDataElement.dataset.title}), true);
            if (!result.result) {
                // make action available again
                button.classList.remove("disabled");
                return;
            }

            // User has confirmed the dialog.
            try {
                Ajax.dboAction("audit", "assets\\data\\asset\\AssetAction")
                    .objectIds([assetId])
                    .payload({
                        data: {
                            comment: result.reason
                        }
                    })
                    .dispatch();
            } finally {
                // make action available again
                button.classList.remove("disabled");

                // show notification
                UiNotification.show();

                EventHandler.fire("de.xxschrandxx.assets.asset", "refresh", {
                    assetIds: [assetId],
                });
            }
        });
    }
}

export default AuditAction;
