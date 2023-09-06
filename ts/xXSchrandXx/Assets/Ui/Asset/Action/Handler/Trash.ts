import TrashDialog from "./Trash/Dialog";

type Callback = () => void;

export class TrashHandler {
    private assetIDs: number[];

    public constructor(assetIDs: number[]) {
        this.assetIDs = assetIDs;
    }

    public trash(callback: Callback): void {
        TrashDialog.open(this.assetIDs, callback);
    }
}

export default TrashHandler;