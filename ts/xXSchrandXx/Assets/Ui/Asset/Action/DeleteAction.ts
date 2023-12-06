import * as Ajax from "WoltLabSuite/Core/Ajax";
import { confirmationFactory } from "WoltLabSuite/Core/Component/Confirmation";

export class DeleteAction {
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

            const result = await confirmationFactory()
                .delete(assetDataElement.dataset.title);
            if (!result) {
                // make action available again
                button.classList.remove("disabled");
                return;
            }

            // User has confirmed the dialog.
            try {
                Ajax.dboAction("delete", "assets\\data\\asset\\AssetAction")
                .objectIds([assetId])
                .dispatch();
            } finally {
                // make action available again
                button.classList.remove("disabled");
            }
        });
    }
}

export default DeleteAction;
