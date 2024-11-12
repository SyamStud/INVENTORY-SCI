from docx2pdf import convert
import sys

pdf_file = "test.pdf"

print(f"File telah berhasil dikonversi ke PDF: {pdf_file}")
if len(sys.argv) != 3:
    print("Usage: python word2pdf.py <input_docx_file> <output_pdf_file>")
    sys.exit(1)

docx_file = sys.argv[1]
pdf_file = sys.argv[2]

convert(docx_file, pdf_file)

print(f"File telah berhasil dikonversi ke PDF: {pdf_file}")