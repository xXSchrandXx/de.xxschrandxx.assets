import AbstractAssetAction from "./Abstract";
import Delete from "./Handler/Delete";

export class DeleteAction extends AbstractAssetAction {
    public constructor(button: HTMLElement, userId: number, assetDataElement: HTMLElement) {
        super(button, userId, assetDataElement);

        if (typeof this.button.dataset.confirmMessage !== "string") {
            throw new Error("The button does not provide a confirmMessage."); // TODO add not list support
        }

        this.button.addEventListener("click", (event) => {
            event.preventDefault();

            if (this.button.hidden) {
                throw Error("No permission!");
            }

            const deleteHandler = new Delete(
                [this.assetId],
                () => {
                    this.assetDataElement.remove();
                },
                this.button.dataset.confirmMessage!,
            );
            deleteHandler.delete();
        });
    }
}

export default DeleteAction;
