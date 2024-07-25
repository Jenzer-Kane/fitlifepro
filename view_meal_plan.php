<?php
include 'database.php';

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$username = $_SESSION['username']; // Get the username from the session

// Query to retrieve meal plans for the logged-in user
$stmt = $mysqli->prepare("SELECT day, time_slot, food_item FROM meal_plans WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

// Organize meal plan data
$meal_plan = array();
while ($row = $result->fetch_assoc()) {
    $meal_plan[$row['day']][] = $row;
}

$days = array_unique(array_column($result->fetch_all(MYSQLI_ASSOC), 'day'));
?>


<?php
include 'view_meal_plan.php';
?>

<!-- Diet Planning Section -->
<section class="our_schedule_section diet-planning">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="our_schedule_content">
                    <h5>DIET PLAN</h5>
                    <h2>RECOMMENDED DIET PLAN FOR<br>
                        <?php echo strtoupper('User Goal'); // Replace with dynamic goal ?>
                    </h2>
                </div>
            </div>
        </div>
        <div class="diet-horizontal-display">
            <?php foreach ($days as $day): ?>
                <form id="mealPlanForm-<?php echo strtolower($day); ?>" action="save_meal_plan.php" method="POST">
                    <div class="border-mealplan">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="our_schedule_content">
                                        <h2 class="mt-5"><?php echo $day; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="diet-horizontal-display">
                            <table class="border border-black" id="mealPlanTable-<?php echo strtolower($day); ?>">
                                <thead>
                                    <tr>
                                        <th>Time Slot</th>
                                        <th>Food Item</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($meal_plan[$day])): ?>
                                        <?php foreach ($meal_plan[$day] as $item): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['time_slot']); ?></td>
                                                <td class="mealItem">
                                                    <br><?php echo htmlspecialchars($item['food_item']); ?><br><br>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="2">No meal plan available for this day.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="total-<?php echo strtolower($day); ?>" class="border border-grey large-counter-text"
                            data-calories="0" data-protein="0">
                            Calories: <span id="calories-<?php echo strtolower($day); ?>">0</span><br>
                            Protein (g): <span id="protein-<?php echo strtolower($day); ?>">0</span><br>
                            <div class="calculator-form form-section border-0">
                                <button type="button" class="shuffle-button"
                                    onclick="shuffleMealPlan('<?php echo strtolower($day); ?>')">Regenerate</button>
                            </div>
                            <div class="note">
                                <b>Meal plan food suggestions are based on the Philippine Department of Science and
                                    Technology, Food and Nutrition Research Institute, Food Exchange List</b>
                            </div>
                        </div>
                        <input type="hidden" name="day" value="<?php echo $day; ?>">
                        <button type="submit">Save Diet Plan for <?php echo $day; ?></button>
                    </div>
                </form>
            <?php endforeach; ?>
        </div>
    </div>
</section>