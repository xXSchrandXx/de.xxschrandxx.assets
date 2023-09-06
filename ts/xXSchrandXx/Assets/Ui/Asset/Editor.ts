import * as Core from "WoltLabSuite/Core/Core";
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";
import AuditAction from "./Action/AuditAction";
import TrashAction from "./Action/TrashAction";
import RestoreAction from "./Action/RestoreAction";
import DeleteAction from "./Action/DeleteAction";

interface RefreshAssetData {
    assetId: number;
}

class UiAssetEditor {
    /**
     * Initializes the edit dropdown for each asset.
     */
    constructor() {
        const asset = document.querySelector(".jsAsset") as HTMLElement;
        if (asset === null) {
            return;
        }
        this.initAsset(asset);

        EventHandler.add("de.xxschrandxx.assets.asset", "refresh", (data: RefreshAssetData) => this.refreshAsset(data));
    }

    /**
     * Initializes the edit dropdown for a asset.
     */
    private initAsset(asset: HTMLElement): void {
        const assetId = ~~asset.dataset.objectId!;

        const auditAsset = document.querySelector(".contentInteractionButton .jsAudit");
        if (auditAsset !== null) {
            new AuditAction(auditAsset as HTMLAnchorElement, assetId, asset);
        }

        const trashAsset = document.querySelector(".contentInteractionButton .jsTrash");
        if (trashAsset !== null) {
            new TrashAction(trashAsset as HTMLAnchorElement, assetId, asset);
        }

        const restoreAsset = document.querySelector(".contentInteractionButton .jsRestore");
        if (restoreAsset !== null) {
            new RestoreAction(restoreAsset as HTMLAnchorElement, assetId, asset);
        }

        const deleteAsset = document.querySelector(".contentInteractionButton .jsDelete");
        if (deleteAsset !== null) {
            new DeleteAction(deleteAsset as HTMLAnchorElement, assetId, asset);
        }
    }

    private refreshAsset(data: RefreshAssetData): void {
        const asset = document.querySelector(".jsAsset") as HTMLElement;
        if (asset === null) {
            return;
        }
        const assetId = ~~asset.dataset.objectId!;
        if (data.assetId != assetId) {
            return;
        }

        const auditAsset = document.querySelector(".contentInteractionButton .jsAudit") as HTMLElement;
        const trashAsset = document.querySelector(".contentInteractionButton .jsTrash") as HTMLElement;
        const restoreAsset = document.querySelector(".contentInteractionButton .jsRestore") as HTMLElement;
        const deleteAsset = document.querySelector(".contentInteractionButton .jsDelete") as HTMLElement;

        const isTrashed = Core.stringToBool(asset.dataset.trashed!);

        if (isTrashed) {
            // Remove buttons
            if (auditAsset !== null) {
                auditAsset.hidden = true;
            }
            if (trashAsset !== null) {
                trashAsset.hidden = true;
            }

            // Add buttons
            if (restoreAsset !== null && asset.dataset.canRestore) {
                restoreAsset.hidden = false;
            }
            if (deleteAsset !== null && asset.dataset.canDelete) {
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
            if (auditAsset !== null && asset.dataset.canAudit) {
                auditAsset.hidden = false;
            }
            if (trashAsset !== null && asset.dataset.canTrash) {
                trashAsset.hidden = false;
            }
        }
    }
}

export = UiAssetEditor;