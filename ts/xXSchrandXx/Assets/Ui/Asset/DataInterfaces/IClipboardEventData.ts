import { ClipboardActionData } from "WoltLabSuite/Core/Controller/Clipboard/Data";

interface IClipboardEventData {
    data: ClipboardActionData;
    listItem: HTMLLIElement;
    responseData: {
        returnValues: string
    }
}

export default IClipboardEventData;
