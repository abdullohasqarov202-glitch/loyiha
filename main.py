"""
SmashBite Telegram bot
Foydalanuvchiga botni ochganda, SmashBite Mini App (veb-sayt)ni
Telegram ichida ochish tugmasini ko'rsatadi.
"""

import os
import logging

from telegram import InlineKeyboardButton, InlineKeyboardMarkup, WebAppInfo, Update
from telegram.ext import Application, CommandHandler, ContextTypes

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# --- Sozlamalar ---
# BOT_TOKEN va WEBAPP_URL Railway'da muhit o'zgaruvchisi (Environment Variable)
# sifatida beriladi. Lokal sinov uchun standart qiymatlar bilan almashtirishingiz mumkin.
BOT_TOKEN = os.getenv("BOT_TOKEN", "8390060267:AAFN43Ig2A5Gtiwi0nwRuhfCGuuTfgRWr1o")
WEBAPP_URL = os.getenv("WEBAPP_URL", "https://sizning-saytingiz.up.railway.app")


async def start(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    """/start buyrug'iga javoban, saytni ochuvchi tugmani ko'rsatadi."""
    keyboard = InlineKeyboardMarkup([
        [InlineKeyboardButton(
            "🍔 Menyuni ochish",
            web_app=WebAppInfo(url=WEBAPP_URL)
        )]
    ])
    await update.message.reply_text(
        "Xush kelibsiz SmashBite botiga! 🔥\n\n"
        "Pastdagi tugma orqali menyuni ko'ring, buyurtma bering "
        "yoki stol band qiling.",
        reply_markup=keyboard,
    )


async def menu_command(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    """/menu buyrug'i ham xuddi shu tugmani ko'rsatadi (qulaylik uchun)."""
    await start(update, context)


async def help_command(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    await update.message.reply_text(
        "Buyruqlar:\n"
        "/start — botni ishga tushirish va menyuni ochish\n"
        "/menu — menyuni qayta ochish\n"
        "/help — shu yordam matni"
    )


def main() -> None:
    if BOT_TOKEN == "BU_YERGA_BOT_TOKENINGIZ":
        logger.warning("DIQQAT: BOT_TOKEN sozlanmagan! Muhit o'zgaruvchisini to'ldiring.")

    application = Application.builder().token(BOT_TOKEN).build()

    application.add_handler(CommandHandler("start", start))
    application.add_handler(CommandHandler("menu", menu_command))
    application.add_handler(CommandHandler("help", help_command))

    logger.info("Bot ishga tushdi...")
    application.run_polling(allowed_updates=Update.ALL_TYPES)


if __name__ == "__main__":
    main()
