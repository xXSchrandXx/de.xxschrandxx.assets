import RestoreDialog from "./Restore/Dialog";

type Callback = () => void;

export class RestoreHandler {
    private assetIDs: number[];

    public constructor(assetIDs: number[]) {
        this.assetIDs = assetIDs;
    }

    public restore(callback: Callback): void {
        RestoreDialog.open(this.assetIDs, callback);
    }
}

export default RestoreHandler;