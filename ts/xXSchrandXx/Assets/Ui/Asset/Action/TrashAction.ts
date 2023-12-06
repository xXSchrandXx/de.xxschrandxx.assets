import * as Ajax from "WoltLabSuite/Core/Ajax";
import { confirmationFactory } from "WoltLabSuite/Core/Component/Confirmation";
import * as Core from "WoltLabSuite/Core/Core";
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";

export class TrashAction {
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

            // check weather the asset is already trashed
            const isTrashed = Core.stringToBool(assetDataElement.dataset.trashed!);
            if (isTrashed) {
                throw Error("Asset is already trashed!");
            }

            const result = await confirmationFactory()
                .softDelete((assetDataElement.dataset.title as string), true);
            if (!result.result) {
                // make action available again
                button.classList.remove("disabled");
                return;
            }

            // User has confirmed the dialog.
            try {
                Ajax.dboAction("trash", "assets\\data\\asset\\AssetAction")
                .objectIds([assetId])
                .payload({
                    data: {
                        reason: result.reason
                    }
                })
                .dispatch();
            } finally {
                // make action available again
                button.classList.remove("disabled");
                // mark as trashed
                assetDataElement.dataset.trashed = "true";

                // show notification
                UiNotification.show();

                EventHandler.fire("de.xxschrandxx.assets.asset", "refresh", {
                    assetIds: [assetId],
                });
            }
        });
    }
}

export default TrashAction;
