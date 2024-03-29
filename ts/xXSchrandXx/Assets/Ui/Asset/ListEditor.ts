import * as ClipboardColtroller from "WoltLabSuite/Core/Controller/Clipboard";
import * as Core from "WoltLabSuite/Core/Core";
import UiDropdownSimple from "WoltLabSuite/Core/Ui/Dropdown/Simple";
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";
import AuditAction from "xXSchrandXx/Assets/Ui/Asset/Action/AuditAction";
import TrashAction from "xXSchrandXx/Assets/Ui/Asset/Action/TrashAction";
import RestoreAction from "xXSchrandXx/Assets/Ui/Asset/Action/RestoreAction";
import DeleteAction from "xXSchrandXx/Assets/Ui/Asset/Action/DeleteAction";
import IRefreshAssetsData from "xXSchrandXx/Assets/Ui/Asset/DataInterfaces/IRefreshAssetsData";

class ListEditor {
    /**
     * Initializes the edit dropdown for each asset.
     */
    constructor() {
        document.querySelectorAll(".jsAssetRow").forEach((assetRow: HTMLTableRowElement) => this.initAsset(assetRow));

        EventHandler.add("de.xxschrandxx.assets.asset", "refresh", (data: IRefreshAssetsData) => this.refreshAssets(data));
    }

    /**
     * Initializes the edit dropdown for a asset.
     */
    private initAsset(assetRow: HTMLTableRowElement): void {
        const assetId = ~~assetRow.dataset.objectId!;
        const dropdownId = `assetListDropdown${assetId}`;
        const dropdownMenu = UiDropdownSimple.getDropdownMenu(dropdownId)!;

        if (dropdownMenu === undefined || dropdownMenu.childElementCount === 0) {
            const toggleButton = assetRow.querySelector(".dropdownToggle") as HTMLAnchorElement;
            toggleButton.classList.add("disabled");

            return;
        }

        const editLink = dropdownMenu.querySelector(".jsEditLink") as HTMLAnchorElement;
        if (editLink !== null) {
            const toggleButton = assetRow.querySelector(".dropdownToggle") as HTMLAnchorElement;
            toggleButton.addEventListener("dblclick", (event) => {
                event.preventDefault();
    
                editLink.click();
            });
        }

        const auditAsset = dropdownMenu.querySelector(".jsAudit");
        if (auditAsset !== null) {
            new AuditAction(auditAsset as HTMLAnchorElement, assetId, assetRow);
        }

        const trashAsset = dropdownMenu.querySelector(".jsTrash");
        if (trashAsset !== null) {
            new TrashAction(trashAsset as HTMLAnchorElement, assetId, assetRow);
        }

        const restoreAsset = dropdownMenu.querySelector(".jsRestore");
        if (restoreAsset !== null) {
            new RestoreAction(restoreAsset as HTMLAnchorElement, assetId, assetRow);
        }

        const deleteAsset = dropdownMenu.querySelector(".jsDelete");
        if (deleteAsset !== null) {
            new DeleteAction(deleteAsset as HTMLAnchorElement, assetId, assetRow);
        }
    }

    private refreshAssets(data: IRefreshAssetsData): void {
        // Reload Clipboard Elements
        if (data.action == 'delete') {
            ClipboardColtroller.unmark("de.xxschrandxx.assets.asset", data.assetIds);
            return;
        } else {
            ClipboardColtroller.reload();
        }

        // Update buttons
        document.querySelectorAll(".jsAssetRow").forEach((assetRow: HTMLTableRowElement) => {
            const assetId = ~~assetRow.dataset.objectId!;
            if (!data.assetIds.includes(assetId)) {
                return;
            }

            const dropdownId = `assetListDropdown${assetId}`;
            const dropdownMenu = UiDropdownSimple.getDropdownMenu(dropdownId)!;

            if (dropdownMenu === undefined || dropdownMenu.childElementCount === 0) {
                return;
            }

            const auditAsset = dropdownMenu.querySelector(".jsAudit") as HTMLElement;
            const trashAsset = dropdownMenu.querySelector(".jsTrash") as HTMLElement;
            const restoreAsset = dropdownMenu.querySelector(".jsRestore") as HTMLElement;
            const deleteAsset = dropdownMenu.querySelector(".jsDelete") as HTMLElement;

            const isTrashed = Core.stringToBool(assetRow.dataset.trashed!);

            if (isTrashed) {
                // Remove buttons
                if (auditAsset !== null) {
                    auditAsset.hidden = true;
                }
                if (trashAsset !== null) {
                    trashAsset.hidden = true;
                }

                // Add buttons
                if (restoreAsset !== null && assetRow.dataset.canRestore) {
                    restoreAsset.hidden = false;
                }
                if (deleteAsset !== null && assetRow.dataset.canDelete) {
                    deleteAsset.hidden = false;
                }
            } else {
                // Remove buttons
                if (restoreAsset !== null) {
                    restoreAsset.hidden = true;
                }
                if (deleteAsset !== null) {
                    deleteAsset.hidden = true;
                }

                // Add buttons
                if (auditAsset !== null && assetRow.dataset.canAudit) {
                    auditAsset.hidden = false;
                }
                if (trashAsset !== null && assetRow.dataset.canTrash) {
                    trashAsset.hidden = false;
                }
            }
        });
    }
}

export = ListEditor;
