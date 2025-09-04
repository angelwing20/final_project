# Restaurant Ingredient Inventory System (RIIS)

## Introduction
The **Restaurant Ingredient Inventory System (RIIS)** is a web-based platform designed to help restaurants and small businesses automate and streamline their inventory management processes.  
Traditionally, many businesses still rely on physical records or spreadsheets to track ingredients, which often leads to data duplication, loss, human error, and inefficient stock handling. RIIS addresses these challenges by providing a secure, reliable, and user-friendly system that manages ingredients, suppliers, categories, and purchase records in real time.  

By integrating automation and digital tracking, RIIS reduces paperwork, minimizes stock miscounts, provides timely low-stock alerts, and ensures centralized data access — all while saving valuable time during daily operations.


### Group Members
- Chin Chun Teng  
- Dennis Lim Wei Yao  
- Wong Yih Jyi  


## Scope
- **Super Admin:**  
  - The Super Admin has full control over the system, including the ability to manage all users such as staff and admins by adding, editing, or removing their accounts.

- **Admin:**  
  - Check current ingredient status, Suggest restocking ingredient, Access basic inventory insights and upload daily sale.




---

RIIS is a modern solution that bridges the gap between manual inventory tracking and digital automation, ensuring efficiency, accuracy, and scalability for growing restaurants.

---

# RIIS — Step-by-Step User Guide

👉 **Access the system:** [Go to Login Page](https://foodventory.synergy-college.org/)

---

![Login Page](Images/final1.png)

## 1) Log In
1. Open the login page.
2. Enter your **email** and **password**.
3. Click **Login** to go to the **Dashboard**.  
4. Forgot your password? Click **Forgot Password**, enter your email, and complete email verification to reset.
5. Default password and email will be superadmin@gmail.com and 123456 for superadmin
6. Default Admin email address and password : admin@gmail.com and 123456
---
![Dash Board Page](Images/dh.png)
## 2) Understand the Dashboard
The Dashboard shows your key stats and quick links:
- **Monthly Revenue**
- **Total Refill Amount**
- **Low Stock Items**
- **Last Daily Sales Upload**
- Charts: **Top 10 Ingredient Consumption (Monthly)** and **Sales Trend (Last 7 Days)**
- **Low-stock ingredient list** at the bottom  

Use the left **sidebar** to navigate: Ingredients, Categories, Menu (Foods & Add-ons), Daily Sales, Refill History, and Staff.

---

## 3) First-Time Setup (Super Admin)
Do these once before daily operations:

### A. Create Ingredient Categories
<img width="1919" height="1024" alt="image" src="https://github.com/user-attachments/assets/f4cc687f-05ac-41ad-86d7-c5ea1ec12aac" />
1. Sidebar → **Ingredients → Categories**
<img width="1917" height="1027" alt="image" src="https://github.com/user-attachments/assets/e96c0d74-e524-48ab-9def-eac9479c8302" />
2. Click **Add Category**, name it, and **Save**


### B. Add Ingredients
<img width="1918" height="1035" alt="image" src="https://github.com/user-attachments/assets/aa3b31fd-d01b-46f9-887f-660330db142e" />
1. Sidebar → **Ingredients → Stock List**
2. Click **Add Ingredient**
<img width="1918" height="1027" alt="image" src="https://github.com/user-attachments/assets/b49486d7-c57e-457f-8830-f5f5269eb41b" />
3. Fill in: **Category**, **Name**, **Unit Type** (weight/quantity), **Stock**, **Minimum Stock**, **Unit per Weight** (needed), **Price**
4. **Save**  

### C. Create Food Categories
<img width="1919" height="1030" alt="image" src="https://github.com/user-attachments/assets/4dfa6e27-0e5d-464e-b07f-d1f8ba9fb3a1" />
1. Sidebar → **Menu → Food Categories**
<img width="1919" height="1030" alt="image" src="https://github.com/user-attachments/assets/7040ffd0-f57b-4706-b07a-7219ffaa93ff" />
2. Click **Add Category**, name it, and **Save**

### D. Add Foods (Menu Items) & Link Ingredients
<img width="1913" height="1027" alt="image" src="https://github.com/user-attachments/assets/6f260c65-16b7-4109-8cec-091804eefa20" />
1. Sidebar → **Menu → Foods**
<img width="1919" height="1026" alt="image" src="https://github.com/user-attachments/assets/6f5402b7-45d7-46f7-8b67-ca4e5949dee1" />
2. Click **Add Food** and enter **Category**, **Name**, **Price**, **Description**
<img width="1919" height="1026" alt="image" src="https://github.com/user-attachments/assets/d5071b4a-4691-4bf9-9b9f-2c073a132d69" />
3. Open the **Food Detail** page
<img width="1626" height="338" alt="image" src="https://github.com/user-attachments/assets/f27fa356-70ae-4944-af65-a5cf5f90bf7a" />
4. Click **Manage Ingredients** → **Add Ingredient**, select items, set **consumption per unit**, and **Save**

### E. Add Add-ons (Optional)
<img width="1919" height="1032" alt="image" src="https://github.com/user-attachments/assets/95e05a0c-a799-41f1-ac23-a8e618cb4e9d" />
1. Sidebar → **Menu → Add-ons**
<img width="1919" height="1029" alt="image" src="https://github.com/user-attachments/assets/228b1448-7981-4ac3-93d4-2d191b9908f4" />
2. Click **Add Add-on**, set **name/price**
<img width="1917" height="851" alt="Add by clicking at the add ingredient" src="https://github.com/user-attachments/assets/a6ea2f75-2624-4602-bf6d-2403f865183b" />
3. Add ingredients consumed by the add-on

### F. Add Staff
<img width="1919" height="994" alt="image" src="https://github.com/user-attachments/assets/384619ef-f97f-4d44-8f49-d42435e21659" />
1. Sidebar → **Staff**
<img width="1917" height="1023" alt="image" src="https://github.com/user-attachments/assets/3c387a18-5522-431f-8166-b30d8d582be6" />
2. Click **Add Staff**, fill in details, choose role (**Admin** or **Super Admin**), and **Save**

---

## 4) Daily Operations (Admin / Super Admin)

### A. Record Daily Sales
<img width="1919" height="1030" alt="image" src="https://github.com/user-attachments/assets/69a687a1-527e-4701-8cb3-39583c0527ff" />
1. Sidebar → **Daily Sales** → **Add New**
![Daily Sales](Images/finaldailysales.png)
2. For each **Food** and **Add-on**, enter the **quantity sold**
3. Totals (quantity and amount) calculate automatically
4. Review the **Ingredient Consumption Preview**:
   - See **Current Stock**, **Consumption**, **Remaining Stock**, **Cost (RM)**
   - Adjust if stock goes negative
5. Click **Submit** to save the record

**To review past sales:**
- Sidebar → **Daily Sales**
- Filter by **Date** or **Staff**, click **Apply** (use **Reset** to clear)

### B. Refill Stock
**Option 1 — from Stock List**
1. Sidebar → **Ingredients → Stock List**
2. Click **Refill** next to an ingredient
3. Enter the **quantity/weight** received, then **Save**

**Option 2 — from Ingredient Detail**
1. Sidebar → **Ingredients → Stock List** → click an ingredient card
2. On **Ingredient Detail**, add a **Refill** entry and **Save**

### C. View Refill History
1. Sidebar → **Refill History**
2. Filter by **Start Date**, **End Date (optional)**, **Staff**, **Ingredient**
3. Click **Apply** or **Reset**

### D. Monitor Low Stock
- Dashboard → check **Low Stock Items** KPI and **low-stock list**
- Click an ingredient to plan refills

---

## 5) Category & Menu Maintenance

### A. Manage Ingredient Categories
- Sidebar → **Ingredients → Categories**
- **Search**, **Add**, **Edit/Delete** categories

### B. Manage Food Categories
- Sidebar → **Menu → Food Categories**
- **Search**, open a category to see foods, **Add/Edit/Delete**

### C. Update Foods & Recipes
- Sidebar → **Menu → Foods** → select a food
- On **Food Detail**, edit details or manage linked ingredients

### D. Manage Add-ons
- Sidebar → **Menu → Add-ons**
- Edit/Delete add-ons and ingredient usage

---

## 6) Staff & Account

### A. Staff Management (Super Admin)
1. Sidebar → **Staff**
2. **Search/Filter** staff; use **Add** to create accounts
3. Open **Staff Detail** to **Change Password** or **Deactivate**
4. Able to 

### B. Your Account
1. Sidebar → **Account → Profile**
2. Click **Edit Profile** to upload image, change **name/email/password**, then **Submit**

---

## 7) Tips & Good Practices
- **Record daily sales before closing** so dashboards and stock stay accurate
- Set **Minimum Stock** properly to trigger **Low-stock alerts**
- Keep recipes updated for correct **Ingredient Consumption Preview**
- Use **Refill History filters** for audits
- If charts show *No data*, make sure **Daily Sales** are entered

