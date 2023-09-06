import UiDialog from "WoltLabSuite/Core/Ui/Dialog";
import { DialogCallbackSetup } from "WoltLabSuite/Core/Ui/Dialog/Data";
import * as Language from "WoltLabSuite/Core/Language";
import * as Ajax from "WoltLabSuite/Core/Ajax";

type Callback = () => void;

export class AuditDialog {
    private static instance: AuditDialog;

    private auditCallback: Callback;
    private assetIDs: number[];
    private submitElement: HTMLElement;
    private commentInput: HTMLInputElement;
    private dialogContent: HTMLElement;

    public static open(assetIDs: number[], callback: Callback): void {
        if (!AuditDialog.instance) {
            AuditDialog.instance = new AuditDialog();
        }

        AuditDialog.instance.setCallback(callback);
        AuditDialog.instance.setAssetIDs(assetIDs);
        AuditDialog.instance.openDialog();
    }

    private openDialog(): void {
        UiDialog.open(this);
    }

    private setCallback(callback: Callback): void {
        this.auditCallback = callback;
    }

    private setAssetIDs(assetIDs: number[]): void {
        this.assetIDs = assetIDs;
    }

    private auditSubmit(comment: string): void {
        Ajax.apiOnce({
            data: {
                actionName: "audit",
                className: "assets\\data\\asset\\AssetAction",
                objectIDs: this.assetIDs,
                parameters: {
                    comment: comment,
                },
            },
            success: this.auditCallback,
        });
    }

    private cleanupDialog(): void {
        this.commentInput.value = "";
    }

    _dialogSetup(): ReturnType<DialogCallbackSetup> {
        return {
            id: "assetAuditHandler",
            options: {
                onSetup: (content: HTMLElement): void => {
                    this.dialogContent = content;
                    this.submitElement = content.querySelector(".formSubmitButton")!;
                    this.commentInput = content.querySelector("#assetAuditComment") as HTMLInputElement;

                    this.submitElement.addEventListener("click", (event) => {
                        event.preventDefault();

                        this.auditSubmit(this.commentInput.value);

                        UiDialog.close(this);

                        this.cleanupDialog();
                    });
                },
                title: Language.get("assets.asset.audit"),
            },
            source: `
<div class="section">
	<dl>
		<dt><label for="assetAuditComment">${Language.get("assets.asset.audit.comment.optional")}</label></dt>
		<dd>
			<textarea id="assetAuditComment" cols="40" rows="3" class=""></textarea>
		</dd>
	</dl>
</div>
<div class="formSubmit dialogFormSubmit">
	<button type="button" class="button buttonPrimary formSubmitButton" accesskey="s">
		${Language.get("wcf.global.button.submit")}
	</button>
</div>`,
        };
    }
}

export default AuditDialog;