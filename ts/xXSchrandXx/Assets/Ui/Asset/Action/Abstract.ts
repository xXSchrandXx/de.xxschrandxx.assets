export abstract class AbstractAssetAction {
    protected readonly button: HTMLElement;
    protected readonly assetDataElement: HTMLElement;
    protected readonly assetId: number;
  
    public constructor(button: HTMLElement, assetId: number, assetDataElement: HTMLElement) {
        this.button = button;
        this.assetId = assetId;
        this.assetDataElement = assetDataElement;
    }
}
  
export default AbstractAssetAction;
