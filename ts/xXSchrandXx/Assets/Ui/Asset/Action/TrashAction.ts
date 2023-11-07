import * as Core from "WoltLabSuite/Core/Core";
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";
import AbstractAssetAction from "./Abstract";
import TrashHandler from "./Handler/Trash";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";

export class TrashAction extends AbstractAssetAction {
    private trashHandler: TrashHandler;

    public constructor(button: HTMLElement, assetId: number, assetDataElement: HTMLElement) {
        super(button, assetId, assetDataElement);

        this.trashHandler = new TrashHandler([this.assetId]);

        this.button.addEventListener("click", (event) => {
            event.preventDefault();

            if (this.button.hidden) {
                throw Error("No permission!");
            }

            const isTrashed = Core.stringToBool(this.assetDataElement.dataset.trashed!);

            if (!isTrashed) {
                this.trashHandler.trash(() => {
                    this.assetDataElement.dataset.trashed = "true";

                    UiNotification.show();

                    EventHandler.fire("de.xxschrandxx.assets.asset", "refresh", {
                        assetIds: [this.assetId],
                    });
                });
            } else {
                throw Error("Asset is already trashed!");
            }
        });
    }
}

export default TrashAction;
