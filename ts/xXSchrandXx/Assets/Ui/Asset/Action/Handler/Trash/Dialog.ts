import UiDialog from "WoltLabSuite/Core/Ui/Dialog";
import { DialogCallbackSetup } from "WoltLabSuite/Core/Ui/Dialog/Data";
import * as Language from "WoltLabSuite/Core/Language";
import * as Ajax from "WoltLabSuite/Core/Ajax";

type Callback = () => void;

export class TrashDialog {
    private static instance: TrashDialog;

    private trashCallback: Callback;
    private assetIDs: number[];
    private submitElement: HTMLElement;
    private reasonInput: HTMLInputElement;
    private dialogContent: HTMLElement;

    public static open(assetIDs: number[], callback: Callback): void {
        if (!TrashDialog.instance) {
            TrashDialog.instance = new TrashDialog();
        }

        TrashDialog.instance.setCallback(callback);
        TrashDialog.instance.setAssetIDs(assetIDs);
        TrashDialog.instance.openDialog();
    }

    private openDialog(): void {
        UiDialog.open(this);
    }

    private setCallback(callback: Callback): void {
        this.trashCallback = callback;
    }

    private setAssetIDs(assetIDs: number[]): void {
        this.assetIDs = assetIDs;
    }

    private trashSubmit(reason: string): void {
        Ajax.apiOnce({
            data: {
                actionName: "trash",
                className: "assets\\data\\asset\\AssetAction",
                objectIDs: this.assetIDs,
                parameters: {
                    reason: reason,
                },
            },
            success: this.trashCallback,
        });
    }

    private cleanupDialog(): void {
        this.reasonInput.value = "";
    }

    _dialogSetup(): ReturnType<DialogCallbackSetup> {
        return {
            id: "assetTrashHandler",
            options: {
                onSetup: (content: HTMLElement): void => {
                    this.dialogContent = content;
                    this.submitElement = content.querySelector(".formSubmitButton")!;
                    this.reasonInput = content.querySelector("#assetTrashReason") as HTMLInputElement;

                    this.submitElement.addEventListener("click", (event) => {
                        event.preventDefault();

                        this.trashSubmit(this.reasonInput.value);

                        UiDialog.close(this);

                        this.cleanupDialog();
                    });
                },
                title: Language.get("assets.asset.trash"),
            },
            source: `
<div class="section">
	<dl>
		<dt><label for="assetTrashReason">${Language.get("wcf.global.reason.optional")}</label></dt>
		<dd>
			<textarea id="assetTrashReason" cols="40" rows="3" class=""></textarea>
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

export default TrashDialog;