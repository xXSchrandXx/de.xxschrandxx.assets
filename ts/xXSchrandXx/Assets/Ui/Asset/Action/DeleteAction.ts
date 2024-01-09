import AbstractAction from "./AbstractAction";
import { confirmationFactory } from "WoltLabSuite/Core/Component/Confirmation";
import { RequestPayload } from "WoltLabSuite/Core/Ajax/Data";

class DeleteAction extends AbstractAction {
    protected actionName = 'delete';
    protected shouldBeTrashed = true;

    public getResult(assetDataElement: HTMLElement): boolean|any {
        return confirmationFactory()
            .delete(assetDataElement.dataset.title);
    }

    public generatePayload(reason: string): RequestPayload {
        return {};
    }

    public afterAction(assetDataElement: HTMLElement, result: unknown): void {
        if (assetDataElement instanceof HTMLTableRowElement) {
            assetDataElement.remove();
            // TODO update ClipboardActions
        } else {
            window.location.href = assetDataElement.dataset.listUrl as string;
        }
    }
}

export = DeleteAction;
