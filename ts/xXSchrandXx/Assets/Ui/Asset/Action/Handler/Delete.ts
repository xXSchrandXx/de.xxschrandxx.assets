import * as UiConfirmation from "WoltLabSuite/Core/Ui/Confirmation";
import * as Ajax from "WoltLabSuite/Core/Ajax";
import { CallbackSuccess } from "WoltLabSuite/Core/Ajax/Data";

export class Delete {
    private assetIDs: number[];
    private successCallback: CallbackSuccess;
    private deleteMessage: string;

    public constructor(assetIDs: number[], successCallback: CallbackSuccess, deleteMessage: string) {
        this.assetIDs = assetIDs;
        this.successCallback = successCallback;
        this.deleteMessage = deleteMessage;
    }

    public delete(): void {
        UiConfirmation.show({
            confirm: () => {
                Ajax.apiOnce({
                    data: {
                        actionName: "delete",
                        className: "assets\\data\\asset\\AssetAction",
                        objectIDs: this.assetIDs,
                    },
                    success: this.successCallback,
                });
            },
            message: this.deleteMessage,
            messageIsHtml: true,
        });
    }
}

export default Delete;