import UiDialog from "WoltLabSuite/Core/Ui/Dialog";
import { DialogCallbackSetup } from "WoltLabSuite/Core/Ui/Dialog/Data";
import * as Language from "WoltLabSuite/Core/Language";
import * as Ajax from "WoltLabSuite/Core/Ajax";

type Callback = () => void;

export class RestoreDialog {
    private static instance: RestoreDialog;

    private restoreCallback: Callback;
    private assetIDs: number[];
    private submitElement: HTMLElement;
    private reasonInput: HTMLInputElement;
    private dialogContent: HTMLElement;

    public static open(assetIDs: number[], callback: Callback): void {
        if (!RestoreDialog.instance) {
            RestoreDialog.instance = new RestoreDialog();
        }

        RestoreDialog.instance.setCallback(callback);
        RestoreDialog.instance.setAssetIDs(assetIDs);
        RestoreDialog.instance.openDialog();
    }

    private openDialog(): void {
        UiDialog.open(this);
    }

    private setCallback(callback: Callback): void {
        this.restoreCallback = callback;
    }

    private setAssetIDs(assetIDs: number[]): void {
        this.assetIDs = assetIDs;
    }

    private restoreSubmit(reason: string): void {
        Ajax.apiOnce({
            data: {
                actionName: "restore",
                className: "assets\\data\\asset\\AssetAction",
                objectIDs: this.assetIDs,
                parameters: {
                    reason: reason,
                },
            },
            success: this.restoreCallback,
        });
    }

    private cleanupDialog(): void {
        this.reasonInput.value = "";
    }

    _dialogSetup(): ReturnType<DialogCallbackSetup> {
        return {
            id: "assetRestoreHandler",
            options: {
                onSetup: (content: HTMLElement): void => {
                    this.dialogContent = content;
                    this.submitElement = content.querySelector(".formSubmitButton")!;
                    this.reasonInput = content.querySelector("#assetRestoreReason") as HTMLInputElement;

                    this.submitElement.addEventListener("click", (event) => {
                        event.preventDefault();

                        this.restoreSubmit(this.reasonInput.value);

                        UiDialog.close(this);

                        this.cleanupDialog();
                    });
                },
                title: Language.get("assets.asset.restore"),
            },
            source: `
<div class="section">
	<dl>
		<dt><label for="assetRestoreReason">${Language.get("wcf.global.reason.optional")}</label></dt>
		<dd>
			<textarea id="assetRestoreReason" cols="40" rows="3" class=""></textarea>
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

export default RestoreDialog;
