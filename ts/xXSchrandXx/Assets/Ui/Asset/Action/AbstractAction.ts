import * as Ajax from "WoltLabSuite/Core/Ajax";
import * as Core from "WoltLabSuite/Core/Core";
import DboAction from "WoltLabSuite/Core/Ajax/DboAction";
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";
import { RequestPayload } from "WoltLabSuite/Core/Ajax/Data";

export abstract class AbstractAction {
    protected abstract actionName: string;
    protected shouldBeTrashed: boolean = false;
    protected showUiNotification: boolean = true;
    protected fireRefreshEvent: boolean = true;

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

            this.validate(assetDataElement);

            const result = await this.getResult(assetDataElement);

            var reason: string = '';
            if (typeof result == 'boolean') {
                if (!result) {
                    // make action available again
                    button.classList.remove("disabled");
                    return;
                }
            } else {
                if (!result.result) {
                    // make action available again
                    button.classList.remove("disabled");
                    return;
                }
                reason = result.reason;
            }

            var action = this.getAction(assetId, this.generatePayload(reason));

            // User has confirmed the dialog.
            try {
                var result2 = await action.dispatch();
                this.afterAction(assetDataElement, result2);
            } finally {
                // make action available again
                button.classList.remove("disabled");

                // show notification
                if (this.showUiNotification) {
                    UiNotification.show();
                }

                if (this.fireRefreshEvent) {
                    EventHandler.fire("de.xxschrandxx.assets.asset", "refresh", {
                        assetIds: [assetId],
                        action: this.actionName
                    });
                }
            }
        });
    }

    public validate(assetDataElement: HTMLElement): void {
        const isTrashed = Core.stringToBool(assetDataElement.dataset.trashed!);
        if (this.shouldBeTrashed) {
            if (!isTrashed) {
                throw Error("Asset is not trashed!");
            }
        } else {
            if (isTrashed) {
                throw Error("Asset is trashed!");
            }
        }
    }

    public abstract getResult(assetDataElement: HTMLElement): boolean|any;

    public abstract generatePayload(reason: string): RequestPayload;

    public getAction(assetId: number, payload: RequestPayload): DboAction {
        return Ajax.dboAction(this.actionName, "assets\\data\\asset\\AssetAction")
            .objectIds([assetId])
            .payload(payload);
    }

    public afterAction(assetDataElement: HTMLElement, result: unknown): void {
        // does nothing
    }
}

export default AbstractAction;
