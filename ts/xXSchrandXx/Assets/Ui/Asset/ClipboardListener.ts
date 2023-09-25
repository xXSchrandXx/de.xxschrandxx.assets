import DomUtil from "@woltlab/d.ts/WoltLabSuite/Core/Dom/Util";
import { ClipboardActionData } from "WoltLabSuite/Core/Controller/Clipboard/Data";
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";

interface EventData {
    data: ClipboardActionData;
    listItem: HTMLLIElement;
    responseData: {
        returnValues: string
    }
}

class ClipboardListener {
    /**
     * Initializes the event listener.
     */
    constructor() {
        EventHandler.add("com.woltlab.wcf.clipboard", "de.xxschrandxx.assets.asset", (data: EventData) => this.listen(data));
    }

    
    private listen(data: EventData): void {
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