
// MARK: - Main View
struct ChurchHomeView: View {
    @EnvironmentObject var authService: AuthenticationService
    @StateObject var announcementCarouselVM = AnnouncementCarouselViewModel()
    @StateObject var upcomingEventsCarouselVM = UpcomingEventsCarouselViewModel()
    @State private var showAddAnnouncement = false
    @Namespace var animation
    @Namespace var animation2
    
    var body: some View {
        if #available(iOS 18.0, *) {
            CustomScrollView { progress in
                ScrollView {
                    VStack {
                        AnnouncementsHeaderView(
                            viewModel: announcementCarouselVM,
                            showAddAnnouncement: $showAddAnnouncement
                        )
                        
                        if #available(iOS 18.0, *) {
                            // Carousel
                            ScrollView(.horizontal, showsIndicators: false) {
                                AnnouncementCarouselView(viewModel: announcementCarouselVM, animation: animation)
                            }
                        } else {
                            AnnouncementCarouselView(viewModel: announcementCarouselVM, animation: animation)
                        }
                        
                        UpcomingEventsHeaderView(
                            viewModel: upcomingEventsCarouselVM
                        )
                        
                        if #available(iOS 18.0, *) {
                            ScrollView(.horizontal, showsIndicators: false) {
                                UpcomingEventCarouselView(viewModel: upcomingEventsCarouselVM, animation: animation2)
                            }
                        } else {
                            UpcomingEventCarouselView(viewModel: upcomingEventsCarouselVM, animation: animation2)
                        }
                        
                        Spacer(minLength: getRect().height * 0.1)
                    }
                }
                .background(ChurchHomeBackgroundView())
                .sheet(item: $announcementCarouselVM.selectedAnnouncement) {
                    withAnimation(.spring()) {
                        announcementCarouselVM.selectedAnnouncement = nil
                        announcementCarouselVM.showAnnouncement = false
                        DispatchQueue.main.asyncAfter(deadline: .now() + 0.3) {
                            withAnimation(.easeIn) {
                                announcementCarouselVM.showContent.toggle()
                            }
                        }
                    }
                    for index in announcementCarouselVM.announcements.indices {
                        withAnimation(.spring()) {
                            announcementCarouselVM.announcements[index].offset = 0
                            announcementCarouselVM.swipedAnnouncement = 0
                        }
                    }
                } content: { selectedAnnouncement  in
                    AnnouncementDetailView(animation: animation)
                        .environmentObject(announcementCarouselVM)
                }
                .sheet(isPresented: $showAddAnnouncement) {
                    AddAnnouncementView()
                        .environmentObject(announcementCarouselVM)
                }
            } sheetContent: { progress in
                DummySheetContentView()
            } bottomBar: { progress in
                BottombarView()
                    .padding(.bottom, 10)
            }
        } else {
            // Fallback on earlier versions
        }
    }
}

// MARK: - Header Views
struct AnnouncementsHeaderView: View {
    @EnvironmentObject var authService: AuthenticationService
    @ObservedObject var viewModel: AnnouncementCarouselViewModel
    @Binding var showAddAnnouncement: Bool
    
    var body: some View {
        HStack {
            GradientText(text: "Announcements")
                .padding(.leading)
            
            Spacer()
            
            if viewModel.swipedAnnouncement > 0 {
                Button {
                    resetViews()
                } label: {
                    Image(systemName: "arrow.counterclockwise")
                        .font(.system(size: 20, weight: .semibold))
                        .foregroundColor(.gray)
                }
                .padding(.trailing)
            }
            
            if authService.userHasAdminRights() {
                Button {
                    showAddAnnouncement = true
                } label: {
                    Image(systemName: "plus.circle.fill")
                        .font(.system(size: 24))
                        .foregroundColor(.blue)
                }
                .padding(.trailing)
            }
        }
        .padding()
    }
    
    private func resetViews() {
        for index in viewModel.announcements.indices {
            withAnimation(.spring()) {
                viewModel.announcements[index].offset = 0
                viewModel.swipedAnnouncement = 0
            }
        }
    }
}

struct UpcomingEventsHeaderView: View {
    @ObservedObject var viewModel: UpcomingEventsCarouselViewModel
    
    var body: some View {
        HStack {
            GradientText(text: "Upcoming Events")
                .foregroundColor(.black)
                .padding(.leading)
            
            Spacer()
            
            if viewModel.swipedEvent > 0 {
                Button {
                    resetViews()
                } label: {
                    Image(systemName: "arrow.counterclockwise")
                        .font(.system(size: 20, weight: .semibold))
                        .foregroundColor(.gray)
                }
                .padding(.trailing)
            }
        }
        .padding()
    }
    
    private func resetViews() {
        for index in viewModel.events.indices {
            withAnimation(.spring()) {
                viewModel.events[index].offset = 0
                viewModel.swipedEvent = 0
            }
        }
    }
}

// MARK: - Carousel Subviews
struct AnnouncementCarouselView: View {
    @ObservedObject var viewModel: AnnouncementCarouselViewModel
    var animation: Namespace.ID
    
    // 1. Explicitly define our screen width for safe calculation
    private let screenWidth = UIScreen.main.bounds.width
    
    var body: some View {
        ZStack {
            ForEach(viewModel.announcements.indices.reversed(), id: \.self) { index in
                HStack {
                    AnnouncementCardView(announcement: viewModel.announcements[index], animation: animation)
                        .frame(
                            width: getCardWidth(),
                            height: getCardHeight(index: index)
                        )
                        .offset(x: getCardOffset(index: index))
                        .rotationEffect(.init(degrees: getCardRotation(index: index)))
                        .environmentObject(viewModel)
                    
                    Spacer(minLength: 0)
                }
                .frame(height: 400)
                .contentShape(Rectangle())
                .offset(x: viewModel.announcements[index].offset)
                .gesture(
                    DragGesture(minimumDistance: 10)
                        .onChanged { value in
                            if abs(value.translation.width) > abs(value.translation.height) {
                                onChanged(value: value, index: index)
                            }
                        }
                        .onEnded { value in
                            if abs(value.translation.width) > abs(value.translation.height) {
                                onEnd(value: value, index: index)
                            }
                        }
                )
            }
        }
        .padding(.top, 25)
        .padding(.horizontal, 30)
    }
    
    private func getCardRotation(index: Int) -> Double {
        let boxWidth = Double(screenWidth / 3)
        let offset = Double(viewModel.announcements[index].offset)
        let angle: Double = 5
        return (offset / boxWidth) * angle
    }
    
    private func getCardHeight(index: Int) -> CGFloat {
        let height: CGFloat = 400
        // 3. Prevent negative indexing for off-screen cards
        let safeIndex = max(0, index - viewModel.swipedAnnouncement)
        let cardHeight = safeIndex <= 2 ? CGFloat(safeIndex) * 35 : 70
        return height - cardHeight
    }
    
    private func getCardWidth() -> CGFloat {
        return screenWidth - 120
    }
    
    private func getCardOffset(index: Int) -> CGFloat {
        // 3. Prevent negative indexing for off-screen cards
        let safeIndex = max(0, index - viewModel.swipedAnnouncement)
        return safeIndex <= 2 ? CGFloat(safeIndex) * 30 : 60
    }
    
    private func onChanged(value: DragGesture.Value, index: Int) {
        // 2. Prevent dragging background cards
        guard index == viewModel.swipedAnnouncement else { return }
        
        if value.translation.width < 0 {
            viewModel.announcements[index].offset = value.translation.width
        }
    }
    
    private func onEnd(value: DragGesture.Value, index: Int) {
        // 2. Prevent ending drag on background cards
        guard index == viewModel.swipedAnnouncement else { return }
        
        withAnimation {
            if -value.translation.width > screenWidth / 3 && viewModel.swipedAnnouncement < viewModel.announcements.count - 1 {
                viewModel.announcements[index].offset = -screenWidth
                viewModel.swipedAnnouncement += 1
            } else {
                viewModel.announcements[index].offset = 0
            }
        }
    }
}

struct UpcomingEventCarouselView: View {
    @ObservedObject var viewModel: UpcomingEventsCarouselViewModel
    var animation: Namespace.ID
    
    // 1. Explicitly define our screen width for safe calculation
    private let screenWidth = UIScreen.main.bounds.width
    
    var body: some View {
        ZStack {
            ForEach(viewModel.events.indices.reversed(), id: \.self) { index in
                HStack {
                    UpcomingEventCardView(event: viewModel.events[index], animation: animation)
                        .frame(
                            width: getCardWidth(),
                            height: getCardHeight(index: index)
                        )
                        .offset(x: getCardOffset(index: index))
                        .rotationEffect(.init(degrees: getCardRotation(index: index)))
                        .environmentObject(viewModel)
                    
                    Spacer(minLength: 0)
                }
                .frame(height: 400)
                .contentShape(Rectangle())
                .offset(x: viewModel.events[index].offset)
                .gesture(
                    DragGesture(minimumDistance: 10)
                        .onChanged { value in
                            if abs(value.translation.width) > abs(value.translation.height) {
                                onChanged(value: value, index: index)
                            }
                        }
                        .onEnded { value in
                            if abs(value.translation.width) > abs(value.translation.height) {
                                onEnd(value: value, index: index)
                            }
                        }
                )
            }
        }
        .padding(.top, 25)
        .padding(.horizontal, 30)
    }
    
    private func getCardRotation(index: Int) -> Double {
        let boxWidth = Double(screenWidth / 3)
        let offset = Double(viewModel.events[index].offset)
        let angle: Double = 5
        return (offset / boxWidth) * angle
    }
    
    private func getCardHeight(index: Int) -> CGFloat {
        let height: CGFloat = 400
        // 3. Prevent negative indexing for off-screen cards
        let safeIndex = max(0, index - viewModel.swipedEvent)
        let cardHeight = safeIndex <= 2 ? CGFloat(safeIndex) * 35 : 70
        return height - cardHeight
    }
    
    private func getCardWidth() -> CGFloat {
        return screenWidth - 120
    }
    
    private func getCardOffset(index: Int) -> CGFloat {
        // 3. Prevent negative indexing for off-screen cards
        let safeIndex = max(0, index - viewModel.swipedEvent)
        return safeIndex <= 2 ? CGFloat(safeIndex) * 30 : 60
    }
    
    private func onChanged(value: DragGesture.Value, index: Int) {
        // 2. Prevent dragging background cards
        guard index == viewModel.swipedEvent else { return }
        
        if value.translation.width < 0 {
            viewModel.events[index].offset = value.translation.width
        }
    }
    
    private func onEnd(value: DragGesture.Value, index: Int) {
        // 2. Prevent ending drag on background cards
        guard index == viewModel.swipedEvent else { return }
        
        withAnimation {
            if -value.translation.width > screenWidth / 3 && viewModel.swipedEvent < viewModel.events.count - 1 {
                viewModel.events[index].offset = -screenWidth
                viewModel.swipedEvent += 1
            } else {
                viewModel.events[index].offset = 0
            }
        }
    }
}

// MARK: - Background & Minor Components
struct ChurchHomeBackgroundView: View {
    var body: some View {
        ZStack {
            Image("background1")
                .resizable()
                .aspectRatio(contentMode: .fill)
                .ignoresSafeArea()
            
            LinearGradient(
                gradient: Gradient(colors: [.red.opacity(0.1), .blue.opacity(0.5)]),
                startPoint: .topLeading,
                endPoint: .bottomTrailing
            )
            
            VisualEffectBlur(blurStyle: .systemUltraThinMaterial, vibrancyStyle: .fill) {
                
            }
        }
        .ignoresSafeArea()
    }
}

struct DummySheetContentView: View {
    var body: some View {
        let fillColor: Color = .primary.opacity(0.07)
        
        VStack(alignment: .leading, spacing: 15) {
            Text("Featured")
                .font(.title.bold())
                .frame(maxWidth: .infinity, alignment: .leading)
                .padding(.bottom, 10)
            
            HStack(spacing: 10) {
                RoundedRectangle(cornerRadius: 15)
                    .fill(fillColor)
                
                RoundedRectangle(cornerRadius: 15)
                    .fill(fillColor)
            }
            .frame(height: 180)
            
            Text("Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.")
                .font(.callout)
                .multilineTextAlignment(.leading)
            
            HStack(spacing: 10) {
                RoundedRectangle(cornerRadius: 15)
                    .fill(fillColor)
                
                RoundedRectangle(cornerRadius: 15)
                    .fill(fillColor)
            }
            .frame(height: 180)
            
            Text("Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old.")
                .font(.callout)
                .multilineTextAlignment(.leading)
                .padding(.top, 10)
        }
        .padding(15)
        .padding(.bottom, 50)
    }
}

struct BottombarView: View {
    var body: some View {
        Text("Home")
            .fontWeight(.medium)
            .padding(.vertical, 8)
            .padding(.horizontal, 15)
            .background(
                .bar.shadow(.drop(color: .gray.opacity(0.5), radius: 5)),
                in: .capsule
            )
            .frame(maxWidth: .infinity)
            .overlay(alignment: .leading, content: {
                HStack {
                    Button {
                        
                    } label: {
                        Image(systemName: "xmark")
                            .fontWeight(.medium)
                            .frame(width: 45, height: 45)
                            .foregroundStyle(Color.primary)
                            .background(
                                .bar.shadow(.drop(color: .gray.opacity(0.5), radius: 5)),
                                in: .circle
                            )
                    }
                    
                    Spacer()
                    
                    Button {
                        
                    } label: {
                        Image(systemName: "ellipsis")
                            .fontWeight(.medium)
                            .frame(width: 45, height: 45)
                            .foregroundStyle(Color.primary)
                            .background(
                                .bar.shadow(.drop(color: .gray.opacity(0.5), radius: 5)),
                                in: .circle
                            )
                    }
                }
                .padding(.horizontal, 15)
            })
    }
}

class UpcomingEventsCarouselViewModel: ObservableObject {
    @Published var events: [UpcomingEvent] = [
        UpcomingEvent(
            title: "Youth Conference: Campus Rush",
            description: """
            Prepare for a supernatural encounter that will set your heart ablaze for God! The Sons & Daughters of Apostle Miz Mzwakhe Tancredi are gathering for our annual Campus Rush Youth Conference, a pivotal weekend dedicated to fellowship, impartation, and divine activation. This is far more than an event; it is a summons to a generation destined for greatness. Under the powerful apostolic grace of our father, Apostle Miz Mzwakhe Tancredi, you will be immersed in dynamic worship that touches the heavens and deep teachings that will equip you to fulfill your God-given mandate. We are believing for a mighty outpouring of the Holy Spirit, where destinies are clarified, gifts are stirred up, and a holy fire is ignited within every attendee.

            This conference is specifically designed for young people who refuse to settle for the ordinary. It is for those who are hungry for a tangible move of God and are ready to be launched into a new dimension of their walk with Christ. Come expecting to receive prophetic direction for your life, your studies, and your future. Join hundreds of other passionate young believers as we contend for a revival that will sweep through our university campuses and our nation. Your life is a prophecy waiting to be fulfilled. Don't miss this divine appointment to be commissioned into your purpose. Register today and secure your place in the rush!
            """,
            style: .conference,
            date: "November 10-12"
        ),
        UpcomingEvent(
            title: "Christmas Celebration Service",
            description: """
            Join the Miz Mzwakhe Tancredi Ministries family for a truly glorious Christmas Celebration Service, where we honor the birth of our King, Jesus Christ. Step into an atmosphere saturated with the tangible presence of God as our renowned worship team leads us in prophetic worship and triumphant Christmas carols that declare the majesty of the season. This special service is a cornerstone of our year, a time where we gather as one body to reflect on the profound love and hope that came to the world through the manger. It is an opportunity for families to unite and for hearts to be filled with the warmth and joy of God’s amazing grace.

            The highlight of our celebration will be a life-changing word delivered by our very own Apostle Miz Mzwakhe Tancredi. Prepare your spirit to receive a fresh revelation of what Christ's birth means for us today and a potent prophetic word to carry you through the festive season and beyond. We are not just remembering a historical event; we are celebrating the living King who reigns forevermore! Bring your loved ones, invite your neighbors, and come experience a Christmas celebration filled with wonder, power, and the glorious good news of our Savior. Let’s make this Christmas the most memorable one yet, together in His presence.
            """,
            style: .holiday,
            date: "December 24"
        ),
        UpcomingEvent(
            title: "New Year's Eve Prophetic Gala",
            description: """
            Do not enter the new year by chance; cross over by divine strategy and apostolic decree! You are cordially invited to our New Year's Eve Prophetic Gala, the most significant night of our calendar. This is a sacred and celebratory assembly where we will shut the gates of the past and prophetically open the doors to the future God has ordained for us. Join Apostle Miz Mzwakhe Tancredi and the entire MTM family for an unforgettable evening of high praise, fervent prayer, and powerful prophetic ministry. This is your moment to receive clear apostolic direction for the year ahead, ensuring you walk in victory, purpose, and unprecedented favor.

            The evening will be filled with an electric atmosphere of faith as we decree and declare the promises of God over our lives, families, careers, and our nation. As the clock strikes midnight, we will not just be celebrating a new date; we will be stepping into a new season, armed with a fresh word from the Lord. This is your opportunity to be positioned for blessing and to receive impartation for supernatural breakthroughs in the coming year. Dress in your finest attire and come prepared to worship, dance, and prophesy your way into a year of more. Secure your place for this landmark event and prepare to be launched into 2025 with power and authority.
            """,
            style: .gala,
            date: "December 31"
        )
    ]

    @Published var swipedEvent = 0
    @Published var showEvent = false
    @Published var selectedEvent: UpcomingEvent? = nil
    @Published var showContent = false // This will handle Detail content
}

struct UpcomingEventCardView: View {
    @EnvironmentObject var model: UpcomingEventsCarouselViewModel
    var event: UpcomingEvent
    var animation: Namespace.ID

    var body: some View {
        VStack {
            Text(event.date)
                .font(.caption)
                .foregroundColor(Color.white.opacity(0.85))
                .frame(maxWidth: .infinity, alignment: .leading)
                .padding()
                .padding(.top, 10)
                .matchedGeometryEffect(id: "Date-\(event.id)", in: animation)

            HStack {
                Text(event.title)
                    .font(.title)
                    .fontWeight(.bold)
                    .foregroundColor(.white)
                    .frame(width: 250, alignment: .leading)
                    .padding()
                    .matchedGeometryEffect(id: "Title-\(event.id)", in: animation)

                Spacer(minLength: 0)
            }

            Spacer(minLength: 0)

            HStack {
                Spacer(minLength: 0)

                if !model.showContent {
                    if #available(iOS 26.0, *) {
                        HStack {
                            Text("Read more")
                            
                            Image(systemName: "arrow.right")
                        }
                        .padding(.horizontal)
                        .padding(.vertical, 8)
                        .glassEffect(in: Capsule())
                    } else {
                            // Fallback on earlier versions
                        HStack {
                            Text("Read more")
                            
                            Image(systemName: "arrow.right")
                        }
                        .padding(.horizontal)
                        .padding(.vertical, 8)
                    }
                }
            }
            .foregroundColor(Color.white.opacity(0.9))
            .padding(30)
        }
        .frame(maxWidth: .infinity, maxHeight: .infinity)
        .background(
            event.style.gradient
                .cornerRadius(25)
                .matchedGeometryEffect(id: "bgColor-\(event.id)", in: animation)
        )
        .onTapGesture {
            withAnimation(.spring()) {
               // if let an = announcement as? Announcement{

               // }
                model.selectedEvent = event
                model.showEvent.toggle()
                DispatchQueue.main.asyncAfter(deadline: .now() + 0.3) {
                    withAnimation(.easeIn) {
                        model.showContent.toggle()
                    }
                }
            }
        }
    }
}

class AnnouncementCarouselViewModel: ObservableObject {
    @Published var swipedAnnouncement = 0
    @Published var showAnnouncement = false
    @Published var selectedAnnouncement: Announcement? = nil
    @Published var showContent = false
    @Published var isLoading = false
    @Published var errorMessage: String? = nil
    @Published var announcements: [Announcement] =  [
        Announcement(
            title: "Online School of Watchers 2026",
            description: "Join Dr. Miz Mzwakhe Tancredi for a prophetic journey. The School of Watchers is a community dedicated to spreading the last day revival and equipping believers for the final harvest.",
            style: .promotional,
            date: Calendar.current.date(from: DateComponents(year: 2026, month: 7, day: 23, hour: 19)) ?? Date(),
            imageUrl: "https://mizmzwakhetancredi.org/wp-content/uploads/school-of-watchers.jpg" // Example URL
        ),
        Announcement(
            title: "The Shekinah Season 2026",
            description: "Experience three days of divine visitation and prophetic revelation. The Shekinah Conference is a landmark event in the MTM calendar—don't miss this encounter with the glory of God.",
            style: .update,
            date: Calendar.current.date(from: DateComponents(year: 2026, month: 8, day: 7)) ?? Date(),
            imageUrl: nil
        ),
        Announcement(
            title: "Transformative Mentorship (MentorPoint)",
            description: "An exclusive six-month journey of spiritual growth, excellence, and intentional transformation under the leadership of Apostle Miz Mzwakhe Tancredi.",
            style: .informational,
            date: Calendar.current.date(from: DateComponents(year: 2026, month: 8, day: 11)) ?? Date(),
            imageUrl: "https://mizmzwakhetancredi.org/wp-content/uploads/mentorship.jpg"
        ),
        Announcement(
            title: "Women In Pursuit with Charisma Tancredi",
            description: "Join Mama Charisma Tancredi for a special mentorship session focused on empowering women to navigate business, relationships, and ministry with grace and strength.",
            style: .promotional,
            date: Date().addingTimeInterval(86400 * 14), // Example: 2 weeks from now
            imageUrl: nil
        ),
        Announcement(
            title: "New Life Mid-Week Power Service",
            description: "Tune in every Wednesday at 8PM SAST for our online LIVE services on YouTube and Facebook. Receive a fresh word to sustain your week.",
            style: .informational,
            date: Date(),
            imageUrl: nil
        )
    ]

    private var db = Firestore.firestore()
    private var listenerRegistration: ListenerRegistration?
    
    init() {
        loadData()
    }

    func loadData() {
        listenerRegistration = db.collection("announcements")
            .order(by: "date", descending: false) // Show soonest events first? Or newest created?
            .addSnapshotListener { (querySnapshot, error) in
                if let error = error {
                    self.errorMessage = "Error getting announcements: \(error.localizedDescription)"
                    return
                }
                
                self.announcements = querySnapshot?.documents.compactMap { document in
                    try? document.data(as: Announcement.self)
                } ?? []
            }
    }
    
    // Function to upload image and save data
    func addAnnouncement(title: String, description: String, date: Date, style: AnnouncementStyle, imageData: Data?) {
        self.isLoading = true
        
        let saveDocument = { (url: String?) in
            let newAnnouncement = Announcement(
                title: title,
                description: description,
                style: style,
                date: date,
                imageUrl: url
            )
            
            do {
                _ = try self.db.collection("announcements").addDocument(from: newAnnouncement)
                self.isLoading = false
            } catch {
                self.errorMessage = "Error saving to Firestore: \(error.localizedDescription)"
                self.isLoading = false
            }
        }
        
        if let imageData = imageData {
            // Upload Image first
            let storageRef = Storage.storage().reference().child("announcement_images/\(UUID().uuidString).jpg")
            
            storageRef.putData(imageData, metadata: nil) { _, error in
                if let error = error {
                    self.errorMessage = "Image upload error: \(error.localizedDescription)"
                    self.isLoading = false
                    return
                }
                
                storageRef.downloadURL { url, error in
                    if let error = error {
                        self.errorMessage = "Error getting URL: \(error.localizedDescription)"
                        self.isLoading = false
                        return
                    }
                    saveDocument(url?.absoluteString)
                }
            }
        } else {
            // Save without image
            saveDocument(nil)
        }
    }
    
    func deleteAnnouncement(announcement: Announcement) {
        guard let id = announcement.id else { return }
        
        // Optional: Delete image from storage if exists (omitted for brevity)
        
        db.collection("announcements").document(id).delete { error in
            if let error = error {
                self.errorMessage = "Error deleting: \(error.localizedDescription)"
            }
        }
    }

    deinit {
        listenerRegistration?.remove()
    }
}

struct AnnouncementCardView: View {
    @EnvironmentObject var announcementCarouselVM: AnnouncementCarouselViewModel
    var announcement: Announcement
    var animation: Namespace.ID

    var body: some View {
        VStack {
            Text(announcement.formattedDate)
                .font(.caption)
                .foregroundColor(Color.white.opacity(0.85))
                .frame(maxWidth: .infinity, alignment: .leading)
                .padding()
                .padding(.top, 10)
                .matchedGeometryEffect(id: "Date-\(announcement.id)", in: animation)

            HStack {
                Text(announcement.title)
                    .font(.title)
                    .fontWeight(.bold)
                    .foregroundColor(.white)
                    .frame(width: 250, alignment: .leading)
                    .padding()
                    .matchedGeometryEffect(id: "Title-\(announcement.id)", in: animation)

                Spacer(minLength: 0)
            }

            Spacer(minLength: 0)

            if #available(iOS 26.0, *) {
                HStack {
                    Spacer(minLength: 0)
                    
                    if !announcementCarouselVM.showContent {
                        HStack {
                            Text("Read more")
                            
                            Image(systemName: "arrow.right")
                        }
                        .padding(.horizontal)
                        .padding(.vertical, 8)
                        .glassEffect(in: Capsule())
                    }
                }
                .foregroundColor(Color.white.opacity(0.9))
                .padding(30)
            } else {
                    // Fallback on earlier versions
                HStack {
                    Spacer(minLength: 0)
                    
                    if !announcementCarouselVM.showContent {
                        Text("Read more")
                        
                        Image(systemName: "arrow.right")
                    }
                }
                .foregroundColor(Color.white.opacity(0.9))
                .padding(30)
            }
        }
        .frame(maxWidth: .infinity, maxHeight: .infinity)
        .background(
            announcement.style.gradient
                .cornerRadius(25)
                .matchedGeometryEffect(id: "bgColor-\(announcement.id)", in: animation)
        )
        .onTapGesture {
            withAnimation(.spring()) {
                announcementCarouselVM.selectedAnnouncement = announcement
                announcementCarouselVM.showAnnouncement.toggle()
                DispatchQueue.main.asyncAfter(deadline: .now() + 0.3) {
                    withAnimation(.easeIn) {
                        announcementCarouselVM.showContent.toggle()
                    }
                }
            }
        }
    }
}

struct Announcement: Identifiable, Codable {
    @DocumentID var id: String?
    var title: String
    var description: String
    var style: AnnouncementStyle
    var date: Date // Changed to Date object for the Picker
    var imageUrl: String? // Added for image upload
    var offset: CGFloat = 0
    
    // Helper to format date for display (matches your UI requirements)
    var formattedDate: String {
        let formatter = DateFormatter()
        formatter.dateStyle = .medium
        formatter.timeStyle = .short
        return formatter.string(from: date)
    }
    
    // Enum coding keys to exclude 'offset' from Firebase
    enum CodingKeys: String, CodingKey {
        case id, title, description, style, date, imageUrl
    }
}

enum AnnouncementStyle: String, Codable, CaseIterable {
    case informational = "Informational"
    case warning = "Warning"
    case update = "Update"
    case promotional = "Promotional"
    
    var displayName: String { rawValue }
    
    var gradient: LinearGradient {
        switch self {
        case .informational:
            return LinearGradient(colors: [.cyan, .blue], startPoint: .top, endPoint: .bottom)
        case .warning:
            return LinearGradient(colors: [.orange, .red], startPoint: .top, endPoint: .bottom)
        case .update:
            return LinearGradient(colors: [.mint, .green], startPoint: .top, endPoint: .bottom)
        case .promotional:
            return LinearGradient(colors: [.purple, .pink], startPoint: .top, endPoint: .bottom)
        }
    }
}

struct UpcomingEvent: Identifiable {
    var id = UUID().uuidString
    var title: String
    var description: String
    var style: EventStyle
    var date: String
    var offset: CGFloat = 0
}

enum EventStyle {
    case conference
    case holiday
    case gala
    
    var gradient: LinearGradient {
        switch self {
        case .conference:
            // Was .pink
            return LinearGradient(colors: [.pink, .purple], startPoint: .topLeading, endPoint: .bottomTrailing)
            
        case .holiday:
            // Was .red
            return LinearGradient(colors: [.red, .orange], startPoint: .topLeading, endPoint: .bottomTrailing)
            
        case .gala:
            // Was .purple
            return LinearGradient(colors: [.purple, .indigo], startPoint: .topLeading, endPoint: .bottomTrailing)
        }
    }
}