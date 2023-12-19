import AbstractAction from "./AbstractAction";
import { confirmationFactory } from "WoltLabSuite/Core/Component/Confirmation";
import { getPhrase } from "WoltLabSuite/Core/Language";
import { RequestPayload } from "@woltlab/d.ts/WoltLabSuite/Core/Ajax/Data";

export class AuditAction extends AbstractAction {
    protected actionName = 'audit';
    protected shouldBeTrashed = false;

    public getResult(assetDataElement: HTMLElement): boolean|any {
        return confirmationFactory()
            .withReason(getPhrase("wcf.dialog.confirmation.audit", {title: assetDataElement.dataset.title}), true);
    }

    public generatePayload(reason: string): RequestPayload {
        return {
            data: {
                comment: reason
            }
        };
    }
}

export default AuditAction;
