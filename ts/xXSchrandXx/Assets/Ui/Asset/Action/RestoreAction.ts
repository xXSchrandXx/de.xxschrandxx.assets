import * as Core from "WoltLabSuite/Core/Core";
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";
import AbstractAssetAction from "./Abstract";
import RestoreHandler from "./Handler/Restore";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";

export class RestoreAction extends AbstractAssetAction {
    private restoreHandler: RestoreHandler;

    public constructor(button: HTMLElement, assetId: number, assetDataElement: HTMLElement) {
        super(button, assetId, assetDataElement);

        this.restoreHandler = new RestoreHandler([this.assetId]);

        this.button.addEventListener("click", (event) => {
            event.preventDefault();

            if (this.button.hidden) {
                throw Error("No permission!");
            }

            const isTrashed = Core.stringToBool(this.assetDataElement.dataset.trashed!);

            if (isTrashed) {
                this.restoreHandler.restore(() => {
                    this.assetDataElement.dataset.trashed = "false";

                    UiNotification.show();

                    EventHandler.fire("de.xxschrandxx.assets.asset", "refresh", {
                        assetIds: [this.assetId],
                    });
                });
            } else {
                throw Error("Asset is not trashed!");
            }
        });
    }
}

export default RestoreAction;
