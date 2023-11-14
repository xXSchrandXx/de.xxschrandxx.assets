import * as EventHandler from "WoltLabSuite/Core/Event/Handler";
import IClipboardEventData from "./DataInterfaces/IClipboardEventData";

class ClipboardListener {
    /**
     * Initializes the event listener.
     */
    constructor() {
        EventHandler.add("com.woltlab.wcf.clipboard", "de.xxschrandxx.assets.asset", (data: IClipboardEventData) => this.listen(data));
    }

    
    private listen(data: IClipboardEventData): void {
        if (data.responseData === null) {
            return;
        }

        if (data.data.actionName != "de.xxschrandxx.assets.asset.getLabel") {
            return;
        }

        let html = window.open("about:blank", "export");
        if (html === null) {
            return;
        }
        html.document.write(data.responseData.returnValues);
        html.document.close();
    }
}

export = ClipboardListener;
