class TimeUtil {
    getCurrentTime() {
        const currentDate = new Date().toLocaleString();
        const posOfComma = currentDate.indexOf(',');

        return currentDate.slice(posOfComma + 2);
    }

    getCurrentTimeWithoutSeconds() {
        const currentTime = this.getCurrentTime();
        return currentTime.slice(0, currentTime.indexOf(':', 4));
    }
}

export const timeUtil = new TimeUtil();