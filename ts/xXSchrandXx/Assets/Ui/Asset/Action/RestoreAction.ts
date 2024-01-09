import AbstractAction from "./AbstractAction";
import { confirmationFactory } from "WoltLabSuite/Core/Component/Confirmation";
import { getPhrase } from "WoltLabSuite/Core/Language";
import { RequestPayload } from "WoltLabSuite/Core/Ajax/Data";

class RestoreAction extends AbstractAction {
    protected actionName = 'restore';
    protected shouldBeTrashed = true;

    public getResult(assetDataElement: HTMLElement): boolean|any {
        return confirmationFactory()
            .withReason(getPhrase("wcf.dialog.confirmation.restore", {title: assetDataElement.dataset.title}), true);
    }

    public generatePayload(reason: string): RequestPayload {
        return {
            data: {
                reason: reason
            }
        };
    }

    public afterAction(assetDataElement: HTMLElement, result: unknown): void {
        // mark as not trashed
        assetDataElement.dataset.trashed = "false";
    }
}

export = RestoreAction;
