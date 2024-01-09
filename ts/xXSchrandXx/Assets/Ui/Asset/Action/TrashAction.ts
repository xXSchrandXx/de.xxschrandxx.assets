import AbstractAction from "./AbstractAction";
import { confirmationFactory } from "WoltLabSuite/Core/Component/Confirmation";
import { RequestPayload } from "WoltLabSuite/Core/Ajax/Data";

class TrashAction extends AbstractAction {
    protected actionName = 'trash';
    protected shouldBeTrashed = false;

    public getResult(assetDataElement: HTMLElement): boolean|any {
        return confirmationFactory()
            .softDelete((assetDataElement.dataset.title as string), true);
    }
    
    public generatePayload(reason: string): RequestPayload {
        return {
            data: {
                reason: reason
            }
        };
    }

    public afterAction(assetDataElement: HTMLElement, result: unknown): void {
        // mark as trashed
        assetDataElement.dataset.trashed = "true";
    }
}

export = TrashAction;
