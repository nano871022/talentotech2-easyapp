from playwright.sync_api import sync_playwright, expect

def run_verification():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page()

        # Navigate to the registration page.
        # The default Angular port is 4200.
        page.goto("http://localhost:4200/register")

        # Wait for the page to load by expecting the heading to be visible.
        expect(page.get_by_role("heading", name="Solicitud Asesoría")).to_be_visible()

        # Fill out the form fields.
        page.get_by_label("Correo Electronico").fill("test.user@example.com")
        page.get_by_label("Nombres").fill("Test User")
        page.get_by_label("Teléfono").fill("1234567890")

        # Select a course.
        page.get_by_role("button", name="Chino").click()

        # Take a screenshot for visual verification.
        page.screenshot(path="jules-scratch/verification/verification.png")

        browser.close()

if __name__ == "__main__":
    run_verification()