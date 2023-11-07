import AuditDialog from "./Audit/Dialog";

type Callback = () => void;

export class AuditHandler {
    private assetIDs: number[];

    public constructor(assetIDs: number[]) {
        this.assetIDs = assetIDs;
    }

    public audit(callback: Callback): void {
        AuditDialog.open(this.assetIDs, callback);
    }
}

export default AuditHandler;
